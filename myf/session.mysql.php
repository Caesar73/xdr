<?php


class FileSessionHandler
{
    private $session_db;
    private $session_expire = 3600;

    function open($savePath, $sessionName)
    {
        return true;
    }

    function close()
    {
        return true;
    }

    function read($id)
    {
        $this->session_db = M("tj_session");
        $where = "session_key='" . $id . "'";
        $res = $this->session_db-> where($where) -> find();
        if ($res) {
            return $res["session_value"];
        } else {
            return false;
        }
    }

    function write($key, $value)
    {
        // dump(array("key"=>$key,"value"=>$value));
        if ($key) {
            $session_db = M("tj_session");
            $where = "session_key='" . $key . "'";
            $res = $session_db -> where($where) -> find();
            $data = array("session_expire" => time() + $this->session_expire, "session_value" => $value);
            if ($res) {
                $session_db -> where("session_key='" . $key . "'") -> update($data);
            } else {
                $data["session_key"] = $key;
                $session_db -> add($data);
            }
        }
    }

    function destroy($id)
    {
       $this->session_db->delete("session_key='".$id."'");
       return true;
    }

    function gc($maxlifetime)
    {
        $max = $maxlifetime ? $maxlifetime : 3600 * 24;
        $this->session_db -> delete("session_expire<" . $max);
        return true;
    }
}

ini_set("session.save_handler","user");$handler = new FileSessionHandler();
session_set_save_handler(
    array($handler, 'open'),
    array($handler, 'close'),
    array($handler, 'read'),
    array($handler, 'write'),
    array($handler, 'destroy'),
    array($handler, 'gc')
    );

// the following prevents unexpected effects when using objects as save handlers
register_shutdown_function('session_write_close');

session_start();
// proceed to set and retrieve values by key from $_SESSION