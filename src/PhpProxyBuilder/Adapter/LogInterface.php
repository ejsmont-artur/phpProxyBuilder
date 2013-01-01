<?php

/**
 * I am not sure what is the best way to isolate dependencies from the library.
 * Maybe we should create sinple wrappers for zf2 and symfony components? 
 * Not sure about that but would like to allow replacement of our components.
 */
interface Log {

    public function log();
}