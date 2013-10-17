<?php

function f_error_handler($errno, $errstr, $errfile, $errline) {
    
    $errorType = array(
        E_DEPRECATED => 'DEPRECATED',
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSING ERROR',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE ERROR',
        E_CORE_WARNING => 'CORE WARNING',
        E_COMPILE_ERROR => 'COMPILE ERROR',
        E_COMPILE_WARNING => 'COMPILE WARNING',
        E_USER_DEPRECATED => 'USER DEPRECATED',
        E_USER_ERROR => 'USER ERROR',
        E_USER_WARNING => 'USER WARNING',
        E_USER_NOTICE => 'USER NOTICE',
        E_STRICT => 'STRICT NOTICE',
        E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR'
    );

    $errtype = array_key_exists($errno, $errorType) ? $errorType[$errno] : 'UNKNOW';
    $message = "{$errfile}:{$errline}; {$errtype}; {$errstr}\n";

    error_log($message, 3, RUTA_LOG);

    return false; # Continue with PHP internal error handler
}

function f_exception_handler($exception) {
    
    $file = $exception->getFile();
    $line = $exception->getLine();
    $message = $exception->getMessage();

    $message = "Exception: {$file}:{$line}; {$message}\n";
    error_log($message, 3, RUTA_LOG);
    
    header('Location: dashboard.php');
}

set_exception_handler('f_exception_handler');
set_error_handler('f_error_handler');
?>
