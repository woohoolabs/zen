<?php
return $this->singletonEntries['X\A'] = new \X\A(
    $this->singletonEntries['X\C'] ?? $this->X__C(),
    $this->singletonEntries['X\C'] ?? $this->X__C()
);
