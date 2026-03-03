<?php

namespace JeffersonGoncalves\MetricsMatomo\Queries;

use DateTimeInterface;
use JeffersonGoncalves\MetricsMatomo\Enums\Period;
use JeffersonGoncalves\MetricsMatomo\Settings\MatomoSettings;

class ReportQuery
{
    private string $apiMethod;

    private ?int $siteId = null;

    private Period $period = Period::Day;

    private string $date = 'today';

    private ?string $segment = null;

    private ?int $limit = null;

    private ?string $sortColumn = null;

    private ?string $sortOrder = null;

    private ?string $language = null;

    private bool $expanded = false;

    private bool $flat = false;

    private ?string $label = null;

    /** @var list<string> */
    private array $showColumns = [];

    /** @var list<string> */
    private array $hideColumns = [];

    /** @var array<string, mixed> */
    private array $extraParams = [];

    public function __construct(string $module, string $method)
    {
        $this->apiMethod = "{$module}.{$method}";
    }

    public static function make(string $module, string $method): self
    {
        return new self($module, $method);
    }

    public function site(int $siteId): self
    {
        $this->siteId = $siteId;

        return $this;
    }

    public function period(Period $period): self
    {
        $this->period = $period;

        return $this;
    }

    public function date(DateTimeInterface|string $date): self
    {
        $this->date = $date instanceof DateTimeInterface
            ? $date->format('Y-m-d')
            : $date;

        return $this;
    }

    public function dateRange(DateTimeInterface|string $from, DateTimeInterface|string $to): self
    {
        $fromStr = $from instanceof DateTimeInterface ? $from->format('Y-m-d') : $from;
        $toStr = $to instanceof DateTimeInterface ? $to->format('Y-m-d') : $to;

        $this->period = Period::Range;
        $this->date = "{$fromStr},{$toStr}";

        return $this;
    }

    public function segment(string $segment): self
    {
        $this->segment = $segment;

        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function sortBy(string $column, string $order = 'desc'): self
    {
        $this->sortColumn = $column;
        $this->sortOrder = $order;

        return $this;
    }

    public function language(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function expanded(bool $expanded = true): self
    {
        $this->expanded = $expanded;

        return $this;
    }

    public function flat(bool $flat = true): self
    {
        $this->flat = $flat;

        return $this;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function showColumns(string ...$columns): self
    {
        array_push($this->showColumns, ...$columns);

        return $this;
    }

    public function hideColumns(string ...$columns): self
    {
        array_push($this->hideColumns, ...$columns);

        return $this;
    }

    public function param(string $key, mixed $value): self
    {
        $this->extraParams[$key] = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toQueryParams(): array
    {
        $settings = app(MatomoSettings::class);

        $params = [
            'method' => $this->apiMethod,
            'idSite' => $this->siteId ?? $settings->site_id,
            'period' => $this->period->value,
            'date' => $this->date,
        ];

        if ($this->segment !== null) {
            $params['segment'] = $this->segment;
        }

        if ($this->limit !== null) {
            $params['filter_limit'] = $this->limit;
        }

        if ($this->sortColumn !== null) {
            $params['filter_sort_column'] = $this->sortColumn;
        }

        if ($this->sortOrder !== null) {
            $params['filter_sort_order'] = $this->sortOrder;
        }

        if ($this->language !== null) {
            $params['language'] = $this->language;
        }

        if ($this->expanded) {
            $params['expanded'] = 1;
        }

        if ($this->flat) {
            $params['flat'] = 1;
        }

        if ($this->label !== null) {
            $params['label'] = $this->label;
        }

        if ($this->showColumns !== []) {
            $params['showColumns'] = implode(',', $this->showColumns);
        }

        if ($this->hideColumns !== []) {
            $params['hideColumns'] = implode(',', $this->hideColumns);
        }

        return array_merge($params, $this->extraParams);
    }
}
