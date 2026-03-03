<?php

namespace JeffersonGoncalves\MetricsMatomo\Data;

class VisitSummary
{
    public function __construct(
        public readonly int $nbVisits,
        public readonly int $nbUniqVisitors,
        public readonly int $nbActions,
        public readonly int $nbUsers,
        public readonly int $bounceCount,
        public readonly float $bounceRate,
        public readonly int $sumVisitLength,
        public readonly int $maxActions,
        public readonly float $nbActionsPerVisit,
        public readonly float $avgTimeOnSite,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            nbVisits: (int) ($data['nb_visits'] ?? 0),
            nbUniqVisitors: (int) ($data['nb_uniq_visitors'] ?? 0),
            nbActions: (int) ($data['nb_actions'] ?? 0),
            nbUsers: (int) ($data['nb_users'] ?? 0),
            bounceCount: (int) ($data['bounce_count'] ?? 0),
            bounceRate: self::parsePercentage($data['bounce_rate'] ?? '0%'),
            sumVisitLength: (int) ($data['sum_visit_length'] ?? 0),
            maxActions: (int) ($data['max_actions'] ?? 0),
            nbActionsPerVisit: (float) ($data['nb_actions_per_visit'] ?? 0),
            avgTimeOnSite: (float) ($data['avg_time_on_site'] ?? 0),
        );
    }

    /**
     * @return array<string, int|float>
     */
    public function toArray(): array
    {
        return [
            'nb_visits' => $this->nbVisits,
            'nb_uniq_visitors' => $this->nbUniqVisitors,
            'nb_actions' => $this->nbActions,
            'nb_users' => $this->nbUsers,
            'bounce_count' => $this->bounceCount,
            'bounce_rate' => $this->bounceRate,
            'sum_visit_length' => $this->sumVisitLength,
            'max_actions' => $this->maxActions,
            'nb_actions_per_visit' => $this->nbActionsPerVisit,
            'avg_time_on_site' => $this->avgTimeOnSite,
        ];
    }

    private static function parsePercentage(string|int|float $value): float
    {
        if (is_string($value)) {
            return (float) rtrim($value, '%');
        }

        return (float) $value;
    }
}
