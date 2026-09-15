<?php declare(strict_types=1);
namespace DataTransfer\Logic;

use DataTransfer\Base\OperationInterface;
class Process{
    public function __construct(string|self $process){}
    public static function from(string|self $process):self{
        return new self($process);
    }
    public function do(OperationInterface $operation):self{
        return $this;
    }
}