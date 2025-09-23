<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class LogController extends Controller
{
    // 日志目录，只允许操作该目录下的 .log 文件
    private function logsPath(): string
    {
        return storage_path('logs');
    }

    private function safeFilename(string $name): ?string
    {
        // 仅允许字母数字、点、下划线、短横线，且必须以 .log 结尾，防止目录穿越
        if (!preg_match('/^[A-Za-z0-9._-]+\\.log$/', $name)) {
            return null;
        }
        return $name;
    }

    public function index()
    {
        return view('admin.logs');
    }

    public function post(Request $request)
    {
        $action = $request->post('action');
        switch ($action) {
            case 'list':
                return $this->list($request);
            case 'view':
                return $this->view($request);
            case 'truncate':
                return $this->truncate($request);
            case 'delete':
                return $this->delete($request);
            default:
                return ['status' => -1, 'message' => '对不起，此操作不存在！'];
        }
    }

    private function list(Request $request)
    {
        $dir = $this->logsPath();
        $files = @scandir($dir) ?: [];
        $data = [];
        foreach ($files as $f) {
            if ($f === '.' || $f === '..') continue;
            if (!preg_match('/\\.log$/', $f)) continue;
            $full = $dir . DIRECTORY_SEPARATOR . $f;
            if (!is_file($full)) continue;
            $data[] = [
                'name' => $f,
                'size' => filesize($full),
                'updated_at' => date('Y-m-d H:i:s', filemtime($full)),
            ];
        }
        usort($data, function ($a, $b) {
            return strcmp($b['updated_at'], $a['updated_at']);
        });
        return ['status' => 0, 'message' => '', 'data' => $data];
    }

    private function view(Request $request)
    {
        $name = (string)$request->post('name');
        $name = $this->safeFilename($name);
        if (!$name) {
            return ['status' => -1, 'message' => '文件名不合法'];
        }
        $lines = intval($request->post('lines', 500));
        $lines = ($lines > 0 && $lines <= 5000) ? $lines : 500;
        $file = $this->logsPath() . DIRECTORY_SEPARATOR . $name;
        if (!is_file($file)) {
            return ['status' => -1, 'message' => '日志不存在'];
        }
        // 高效尾读指定行数
        $content = $this->tailFile($file, $lines);
        // 只返回文本
        return ['status' => 0, 'message' => '', 'data' => [
            'name' => $name,
            'content' => $content,
        ]];
    }

    private function truncate(Request $request)
    {
        $name = (string)$request->post('name');
        $name = $this->safeFilename($name);
        if (!$name) {
            return ['status' => -1, 'message' => '文件名不合法'];
        }
        $file = $this->logsPath() . DIRECTORY_SEPARATOR . $name;
        if (!is_file($file)) {
            return ['status' => -1, 'message' => '日志不存在'];
        }
        $fp = @fopen($file, 'w');
        if ($fp === false) {
            return ['status' => -1, 'message' => '清空失败，权限不足或文件被占用'];
        }
        fclose($fp);
        return ['status' => 0, 'message' => '已清空'];
    }

    private function delete(Request $request)
    {
        $name = (string)$request->post('name');
        $name = $this->safeFilename($name);
        if (!$name) {
            return ['status' => -1, 'message' => '文件名不合法'];
        }
        $file = $this->logsPath() . DIRECTORY_SEPARATOR . $name;
        if (!is_file($file)) {
            return ['status' => -1, 'message' => '日志不存在'];
        }
        if (!@unlink($file)) {
            return ['status' => -1, 'message' => '删除失败，权限不足或文件被占用'];
        }
        return ['status' => 0, 'message' => '已删除'];
    }

    public function download(Request $request)
    {
        $name = (string)$request->query('name');
        $name = $this->safeFilename($name);
        if (!$name) {
            abort(400, 'Invalid filename');
        }
        $file = $this->logsPath() . DIRECTORY_SEPARATOR . $name;
        if (!is_file($file)) {
            abort(404);
        }
        // 返回下载响应
        return response()->download($file, $name, [
            'Content-Type' => 'text/plain; charset=UTF-8'
        ]);
    }

    private function tailFile(string $file, int $lines = 500): string
    {
        $f = @fopen($file, 'rb');
        if ($f === false) {
            return '';
        }
        $buffer = '';
        $chunkSize = 4096;
        $pos = -1;
        $lineCount = 0;
        $stat = fstat($f);
        $fileSize = $stat ? intval($stat['size']) : 0;
        $seek = 0 - min($chunkSize, $fileSize);
        while ($lineCount <= $lines && -$seek <= $fileSize) {
            fseek($f, $seek, SEEK_END);
            $chunk = fread($f, min($chunkSize, $fileSize + $seek));
            $buffer = $chunk . $buffer;
            $lineCount = substr_count($buffer, "\n");
            if ($lineCount >= $lines || -$seek === $fileSize) {
                break;
            }
            $seek -= $chunkSize;
        }
        fclose($f);
        $rows = explode("\n", $buffer);
        $rows = array_slice($rows, -$lines);
        return implode("\n", $rows);
    }
}