<?php
        return new \A(
            $this->singletonEntries['B'] ?? $this->B(),
            $this->singletonEntries['C'] ?? $this->C()
        );
