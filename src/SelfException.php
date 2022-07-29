<?php


namespace wksnow;

use think\facade\Log;

class SelfException extends \Exception
{
    public function saveMyErrorTrace() {
        $data = $this->getTrace();
        $trace = array_slice($data, 0, 5);
        $myTrace = json_encode($trace);

        $data = [
            'file'    => $this->getFile(),
            'line'    => $this->getLine(),
            'message' => $this->getMessage(),
            'code'    => $this->getCode(),
        ];
        $log = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]" . PHP_EOL . $myTrace;
        Log::error($log);
    }
}