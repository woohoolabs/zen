<?php
return $this->setClassProperties(
    new \X\A(
        $this->singletonEntries['X\B'] ?? $this->singletonEntries['X\B'] = new \X\B(),
    ),
    [
        'b' => $this->singletonEntries['X\B'] ?? $this->singletonEntries['X\B'] = new \X\B(),
    ]
);
