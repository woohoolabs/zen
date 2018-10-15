<?php
        $entry = new \X\A();
        $this->setProperties(
            $entry,
            [
                'b' => $this->singletonEntries['X\B'] ?? $this->X__B(),
                'c' => $this->singletonEntries['X\C'] ?? $this->X__C(),
            ]
        );

        return $entry;
