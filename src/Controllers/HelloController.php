<?php

namespace Numok\Controllers;

class HelloController extends Controller {
    public function index(): void {
        $this->view('hello/index', [
            'title' => 'Welcome to Numok',
            'message' => 'Hello World!'
        ]);
    }
}