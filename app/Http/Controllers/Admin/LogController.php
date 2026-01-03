<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class LogController extends Controller
{
    /**
     * Display a listing of log files
     */
    public function index(Request $request)
    {
        $logPath = storage_path('logs');
        $logFiles = $this->getLogFiles($logPath);
        
        // Get selected log file (default to latest)
        $selectedFile = $request->get('file', $logFiles[0]['name'] ?? null);
        
        // Get filter parameters
        $level = $request->get('level');
        $search = $request->get('search');
        $date = $request->get('date');
        
        $logs = [];
        $fileContent = '';
        
        if ($selectedFile && File::exists($logPath . '/' . $selectedFile)) {
            $fileContent = File::get($logPath . '/' . $selectedFile);
            $logs = $this->parseLogFile($fileContent);
            
            // Apply filters
            if ($level) {
                $logs = array_filter($logs, function($log) use ($level) {
                    return strtolower($log['level']) === strtolower($level);
                });
            }
            
            if ($search) {
                $logs = array_filter($logs, function($log) use ($search) {
                    return stripos($log['message'], $search) !== false ||
                           stripos($log['trace'] ?? '', $search) !== false;
                });
            }
            
            if ($date) {
                $logs = array_filter($logs, function($log) use ($date) {
                    return strpos($log['date'], $date) === 0;
                });
            }
            
            // Reverse to show newest first
            $logs = array_reverse($logs);
        }
        
        return view('admin.logs.index', compact('logFiles', 'selectedFile', 'logs', 'level', 'search', 'date'));
    }
    
    /**
     * Download a log file
     */
    public function download($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath)) {
            abort(404);
        }
        
        return response()->download($logPath);
    }
    
    /**
     * Delete a log file
     */
    public function delete($filename)
    {
        $logPath = storage_path('logs/' . $filename);
        
        if (!File::exists($logPath)) {
            return redirect()->route('admin.logs.index')->with('error', 'Log file not found');
        }
        
        // Prevent deleting today's log
        if ($filename === 'laravel-' . date('Y-m-d') . '.log' || $filename === 'laravel.log') {
            return redirect()->route('admin.logs.index')->with('error', 'Cannot delete current log file');
        }
        
        File::delete($logPath);
        
        return redirect()->route('admin.logs.index')->with('success', 'Log file deleted successfully');
    }
    
    /**
     * Clear all old logs (keep last 7 days)
     */
    public function clear()
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        $deletedCount = 0;
        
        $cutoffDate = Carbon::now()->subDays(7);
        
        foreach ($files as $file) {
            $filename = $file->getFilename();
            
            // Skip current log file
            if ($filename === 'laravel-' . date('Y-m-d') . '.log' || $filename === 'laravel.log') {
                continue;
            }
            
            // Extract date from filename (laravel-YYYY-MM-DD.log)
            if (preg_match('/laravel-(\d{4}-\d{2}-\d{2})\.log/', $filename, $matches)) {
                $fileDate = Carbon::parse($matches[1]);
                
                if ($fileDate->lt($cutoffDate)) {
                    File::delete($file->getPathname());
                    $deletedCount++;
                }
            }
        }
        
        return redirect()->route('admin.logs.index')->with('success', "Cleared {$deletedCount} old log files");
    }
    
    /**
     * Get all log files with metadata
     */
    private function getLogFiles($path)
    {
        if (!File::exists($path)) {
            return [];
        }
        
        $files = File::files($path);
        $logFiles = [];
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                $logFiles[] = [
                    'name' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'modified' => Carbon::createFromTimestamp($file->getMTime())->diffForHumans(),
                    'date' => Carbon::createFromTimestamp($file->getMTime())->format('Y-m-d H:i:s'),
                ];
            }
        }
        
        // Sort by date (newest first)
        usort($logFiles, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });
        
        return $logFiles;
    }
    
    /**
     * Parse log file content into structured array
     */
    private function parseLogFile($content)
    {
        $logs = [];
        $pattern = '/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(\w+): (.*?)(?=\[\d{4}-\d{2}-\d{2}|\Z)/s';
        
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $message = trim($match[3]);
            $trace = '';
            
            // Extract stack trace if present
            if (strpos($message, 'Stack trace:') !== false) {
                $parts = explode('Stack trace:', $message, 2);
                $message = trim($parts[0]);
                $trace = isset($parts[1]) ? trim($parts[1]) : '';
            } elseif (preg_match('/\n(#\d+.*)/s', $message, $traceMatch)) {
                $parts = explode($traceMatch[0], $message, 2);
                $message = trim($parts[0]);
                $trace = trim($traceMatch[0]);
            }
            
            // Extract file and line from trace or message
            $file = '';
            $line = '';
            
            if (preg_match('/in (.+):(\d+)/', $message . ' ' . $trace, $fileMatch)) {
                $file = $fileMatch[1];
                $line = $fileMatch[2];
            } elseif (preg_match('/([\/\w\-\.]+\.php):(\d+)/', $message . ' ' . $trace, $fileMatch)) {
                $file = $fileMatch[1];
                $line = $fileMatch[2];
            }
            
            $logs[] = [
                'date' => $match[1],
                'level' => strtoupper($match[2]),
                'message' => $message,
                'trace' => $trace,
                'file' => $file,
                'line' => $line,
            ];
        }
        
        return $logs;
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
