<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use Trismegiste\Socialist\Commentary;
use Trismegiste\Socialist\Author;

class CommentaryIterator implements \Iterator, \ArrayAccess, \Countable
{

    protected $wrapped;
    protected $ptr = 0;

    public function __construct(array& $arr)
    {
        $this->wrapped = &$arr;
    }

    public function count($mode = 'COUNT_NORMAL')
    {
        return count($this->wrapped);
    }

    /**
     * @return Trismegiste\Socialist\Commentary
     */
    public function current()
    {
        return $this->wrapped[$this->ptr];
    }

    public function key()
    {
        return $this->current()->getUuid();
    }

    public function next()
    {
        $this->ptr++;
    }

    public function offsetExists($offset)
    {
        foreach ($this->wrapped as $comm) {
            if ($comm->getUuid() === $offset) {
                return true;
            }
        }

        return false;
    }

    public function offsetGet($offset)
    {
        foreach ($this->wrapped as $comm) {
            if ($comm->getUuid() === $offset) {
                return $comm;
            }
        }

        trigger_error("Uuid $offset does not exist", E_USER_NOTICE);
    }

    public function offsetSet($offset, $value)
    {
        throw new \LogicException('Not a good idea: use an offsetGet and modify the object itself');
    }

    public function offsetUnset($offset)
    {
        foreach ($this->wrapped as $idx => $comm) {
            if ($comm->getUuid() === $offset) {
                unset($this->wrapped[$idx]);
                break;
            }
        }
    }

    public function rewind()
    {
        $this->ptr = 0;
    }

    public function valid()
    {
        return (array_key_exists($this->ptr, $this->wrapped));
    }

}

$comment = [];
for ($k = 0; $k < 1000; $k++) {
    $obj = new Commentary(new Author("user $k"));
    $obj->setMessage("aaaaaaaaaaaaa $k");
    $comment[$k] = $obj;
}

$stop = microtime(true);
printf("%.0f kB\n", memory_get_peak_usage(true) / 1e3);

$it = new CommentaryIterator($comment);
//echo $it->count() . PHP_EOL;
printf("%.0f kB\n", memory_get_peak_usage(true) / 1e3);
$it[] = 'oto';
