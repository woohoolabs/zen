<?php
        $entry = $this->singletonEntries['B'] ?? $this->B();

        return $this->singletonEntries['A'] = $entry;
