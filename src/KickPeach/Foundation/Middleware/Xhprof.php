<?php

namespace Kickpeach\Framework\Foundation\Middleware;

use Closure;
use Kickpeach\Framework\Contracts\Application;
/**
 * Class Xhprof
 *
 */
class Xhprof
{
    protected $xhprof;

    public function handle(Application $app, Closure $next)
    {
        // var_dump(array_slice(func_get_args(), 2));  //其余参数
        $this->xhprof = new XhprofHelper(true, $app->config('xhprof_dir'));
        $this->xhprof->begin();

        $response = $next($app);

        $this->xhprof->finish();

        //do something
        return $response;
    }
}

/**
 * xhprof 统计
 */
class XhprofHelper
{
    /**
     * isdebug
     */
    protected $debug = false;

    /**
     * isdebug
     */
    protected $xhprofRoot;

    /**
     * xhprofNamespace
     */
    protected $xhprofNamespace;

    /**
     * xhprofIgnoreFun
     */
    protected $xhprofIgnoreFun = array('define', 'trim');

    public function __construct($debug, $xhprofRoot, $xhprofIgnoreFun = null)
    {
        $this->debug = $debug;

        $this->xhprofRoot = $xhprofRoot;

        if (! empty($xhprofIgnoreFun)) {
            $this->xhprofIgnoreFun = $xhprofIgnoreFun;
        }
    }

    /**
     * xhprof开启
     */
    public function begin()
    {
        if (extension_loaded('tideways')) { //xhprof 扩展
            include $this->xhprofRoot . "/xhprof_lib/utils/xhprof_lib.php";
            include $this->xhprofRoot . "/xhprof_lib/utils/xhprof_runs.php";
            $this->xhprofNamespace = 'TFramework';
            //让xhprof显示cpu
            tideways_enable(TIDEWAYS_FLAGS_CPU + TIDEWAYS_FLAGS_MEMORY, $this->xhprofIgnoreFun);
            //xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY, $this->xhprofIgnoreFun);
        }
    }

    /**
     * xhprof结束
     */
    public function finish()
    {
        if (! empty($this->xhprofNamespace)) {
            $xhprof_data = tideways_disable();
            //$xhprof_data = xhprof_disable();
            // print_r($xhprof_data);exit;
            $xhprof_runs = new \XHProfRuns_Default();
            $run_id = $xhprof_runs->save_run($xhprof_data, $this->xhprofNamespace);
            if ($this->debug) {
                // url to the XHProf UI libraries (change the host name and path)
                $profiler_url = sprintf('/xhprof/xhprof_html/index.php?run=%s&source=%s', $run_id, $this->xhprofNamespace);
                $css = 'position:absolute; right:0; top:0; background:orange; padding:8px;';
                echo '<p style="', $css, '">', '<a href="', $profiler_url, '" target="_blank">Profiler output</a></p>';
            } else {    //@todo 正式环境记录日志 或则 直接将日志push到 prism 中
                // $log = array(
                //     'REQUEST_URI' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '->' . $routes['action'],
                //     'HTTP_HOST' => php_uname('n'),
                //     'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'],
                //     'REQUEST_TIME' => $time,
                //     'xhprof_data' => xhprof_disable()
                // );
                //
                // $log = msgpack_pack($log);
                // $key = 'cf-xhprof-log';
                //
                //
                // $this->ctx->getXhprofRedis()->publish($key, $log);
            }
        }
    }
}
