<?php
        return $this->setClassProperties(
            new \X\A(
                $this->singletonEntries['X\B'] ?? new \X\B()
            ),
            [
                'b' => $this->singletonEntries['X\B'] ?? new \X\B(),
                'c' => $this->singletonEntries['X\C'] ?? $this->setClassProperties(
                    new \X\C(
                        new \X\D()
                    ),
                    [
                        'e' => new \X\E(),
                    ]
                ),
            ]
        );
