<?php
return new \X\A(
    $this->singletonEntries['X\B'] ?? new \X\B(),
    $this->singletonEntries['X\C'] ?? new \X\C()
);
