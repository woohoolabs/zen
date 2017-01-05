<?php

        return $this->singletonEntries['A'] = $this->singletonEntries['B'] ?? $this->B();
