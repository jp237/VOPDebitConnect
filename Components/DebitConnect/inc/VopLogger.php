<?php
namespace Psr\Log;

class VOPLogger extends AbstractLogger
{
    public $msg_warning = array();
    public $msg_error = array();
    public $msg_debug = array();
    public $msg_info = array();
    public $msg_notice = array();
    public $test = "21";



    public function log($level, $message, array $context = array())
    {
        switch ($level) {
            case LogLevel::ERROR:
                $this->msg_error[] = $message;
                break;
            case LogLevel::WARNING:
                $this->msg_warning[] = $message;
                break;
            case LogLevel::NOTICE:
                $this->msg_notice[] = $message;
                break;
            case LogLevel::INFO:
                $this->msg_info[] = $message;
                break;
            case LogLevel::DEBUG:
                $this->msg_debug[]= $message;
                break;
            default:
                break;
        }
    }
}