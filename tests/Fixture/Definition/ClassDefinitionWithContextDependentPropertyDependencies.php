<?php
return $this->singletonEntries['X\A'] = $this->setClassProperties(
    new \X\A(),
    [
        'b' => $this->singletonEntries['X\C'] ?? $this->X__C(),
        'c' => $this->singletonEntries['X\C'] ?? $this->X__C(),
    ]
);
