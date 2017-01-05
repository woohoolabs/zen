<?php
        $entry = new \A();
        $this->setProperties(
            $entry,
            [
                'b' => $this->singletonEntries['B'] ?? $this->B(),
                'c' => $this->singletonEntries['C'] ?? $this->C(),
            ]
        );

        return $entry;
