<?php
        $entry = new \X\A();
        $this->setProperties(
            $entry,
            [
                'b' => $this->singletonEntries['X\C'] ?? $this->X__C(),
                'c' => $this->singletonEntries['X\C'] ?? $this->X__C(),
            ]
        );
        return $this->singletonEntries['X\A'] = $entry;
