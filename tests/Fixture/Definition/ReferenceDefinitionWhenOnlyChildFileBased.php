<?php
return $this->singletonEntries['X\A'] = $this->singletonEntries['X\B'] ?? require __DIR__ . '/Definitions/X__B.php';
