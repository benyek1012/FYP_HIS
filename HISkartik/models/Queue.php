<?php

namespace app\models;
use Yii;

class Queue
{
    public $front;
    public $rear;
  
    public $queue = array();
  
    function __construct() {
      $this->rear = -1;
      $this->front = -1;
    }
  
    // create a function to check whether 
    // the queue is empty or not 
    public function isEmpty() {
      if($this->rear == $this->front) {
        return true;
      } else {
      return false;
      }
    }
  
    //create a function to return size of the queue 
    public function size() {
       return ($this->rear - $this->front);
    }
  
    //create a function to add new element  
    public function EnQueue($x) {
      $this->queue[++$this->rear] = $x;
    //  echo $x." is added into the queue. \n";
    }
  
    //create a function to delete front element  
    public function DeQueue() {
      if($this->rear == $this->front){
        // echo "Queue is empty. \n";
      } else {
        $x = $this->queue[++$this->front];
      //  echo $x." is deleted from the queue. \n";
      }
    }
  
    //create a function to get front element  
    public function frontElement() {
      if($this->rear == $this->front) {
        // echo "Queue is empty. \n";
      } else {
        return $this->queue[$this->front+1];
      }
    }
  
    //create a function to get rear element   
    public function rearElement() {
      if($this->rear == $this->front) {
        echo "Queue is empty. \n";
      } else {
        return $this->queue[$this->rear];
      }
    }
}