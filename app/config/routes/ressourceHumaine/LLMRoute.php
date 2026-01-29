<?php

use app\controllers\LLMController;

Flight::route('/llm', [new LLMController(), 'index']);
Flight::route('POST /llm/ask', [new LLMController(), 'ask']);
