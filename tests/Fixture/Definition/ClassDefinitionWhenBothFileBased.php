<?php
return $this->singletonEntries['X\A'] = new \X\A(
    $this->singletonEntries['X\B'] ?? require __DIR__ . '/X__B.php'
);
