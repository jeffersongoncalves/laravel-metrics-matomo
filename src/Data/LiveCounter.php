<?php

namespace JeffersonGoncalves\MetricsMatomo\Data;

class LiveCounter
{
    public function __construct(
        public readonly int $visits,
        public readonly int $actions,
        public readonly int $visitors,
        public readonly int $visitsConverted,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            visits: (int) ($data['visits'] ?? 0),
            actions: (int) ($data['actions'] ?? 0),
            visitors: (int) ($data['visitors'] ?? 0),
            visitsConverted: (int) ($data['visitsConverted'] ?? 0),
        );
    }

    /**
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'visits' => $this->visits,
            'actions' => $this->actions,
            'visitors' => $this->visitors,
            'visits_converted' => $this->visitsConverted,
        ];
    }
}
