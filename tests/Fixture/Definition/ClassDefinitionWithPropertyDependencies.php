<?php
return $this->setClassProperties(
    new \X\A(),
    [
        'b' => $this->singletonEntries['X\B'] ?? new \X\B(),
        'c' => $this->singletonEntries['X\C'] ?? new \X\C(),
    ]
);
