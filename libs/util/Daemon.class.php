<?php
/*
 *  守护进程
 */

declare(ticks = 1);

//array_shift($argv);

abstract class Daemon {
    private $fn = '';
    private $pids = array();


    /*守护进程继承类需实现业务*/
    abstract public function task();


    # 子进程退出时会触发这个函数
    public function signal_handler($sig) {
        switch ($sig) {
        case SIGINT:
            posix_kill(0, SIGINT);
            break;
        case SIGCHLD:
            break;
        case SIGUSR1:
        }
    }

    # 注册子进程退出的函数
    public function signal($sig, $fn, $restart= true) {
        pcntl_signal($sig, $fn, $restart);
    }

    # 分发
    public function dispatch() {
        pcntl_signal_dispatch();
        return true;
    }

    # fork
    public function fork() {
        return $pid = pcntl_fork();
    }

    public function get_pid() {
        return posix_getpid();
    }

    public function get_ppid() {
        return posix_getppid();
    }

    public function alarm($time) {
        pcntl_alarm(5);
    }

    public function set_sid() {
        return posix_setsid();
    }

    private function break_term() {
        chdir('/');
        umask(0);
    }

    public function begin( $count = 1) {

        /*信号监控*/
        /*
        $this->signal(SIGALRM, array($this, "signal_handler"), true);
        $this->signal(SIGUSR1, array($this, "signal_handler"), true);
        $this->signal(SIGCHLD, array($this, "signal_handler"), true);
        */

        /*
        if (($pid = $this->fork()) != 0) {
            exit();
        }

        if (!$this->set_sid()) {
            exit();
        }
        */
        $this->break_term();

        for ($i = 0; $i < $count; $i++) {

            $pid = $this->fork();

            if ($pid == -1) {
                exit();
            }

            if ($pid > 0) {
                $this->pids[$pid] = 1;
            }

            if ($pid == 0) {
                break;
            }

        }

        # do sth.
        while (1) {
            /*  父进程监控子进程 */
            if ($pid > 0) {
                $cid = pcntl_waitpid(0, $status, WNOHANG);
                if ($cid > 0) {
                    unset($this->pids[$cid]);
                    $this->fork();
                }
            }

            /* 子进程逻辑 */
            if ($pid == 0){
                if ($this->get_ppid() == 1) {
                    exit();
                }

                $this->task();
            }

            usleep(100);
        }
    }
}

?>
