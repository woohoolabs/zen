<?php
        $entry = new \A(
            $this->singletonEntries['B'] ?? $this->B(),
            $this->singletonEntries['C'] ?? $this->C()
        );

        return $entry;
