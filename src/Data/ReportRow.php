<?php

namespace JeffersonGoncalves\MetricsMatomo\Data;

class ReportRow
{
    /**
     * @param  array<string, mixed>  $extra
     */
    public function __construct(
        public readonly string $label,
        public readonly int $nbVisits,
        public readonly int $nbUniqVisitors,
        public readonly int $nbActions,
        public readonly array $extra = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        $label = (string) ($data['label'] ?? '');
        $nbVisits = (int) ($data['nb_visits'] ?? 0);
        $nbUniqVisitors = (int) ($data['nb_uniq_visitors'] ?? 0);
        $nbActions = (int) ($data['nb_actions'] ?? 0);

        $extra = array_diff_key($data, array_flip(['label', 'nb_visits', 'nb_uniq_visitors', 'nb_actions']));

        return new self(
            label: $label,
            nbVisits: $nbVisits,
            nbUniqVisitors: $nbUniqVisitors,
            nbActions: $nbActions,
            extra: $extra,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_merge([
            'label' => $this->label,
            'nb_visits' => $this->nbVisits,
            'nb_uniq_visitors' => $this->nbUniqVisitors,
            'nb_actions' => $this->nbActions,
        ], $this->extra);
    }
}
