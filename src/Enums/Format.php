<?php

namespace JeffersonGoncalves\MetricsMatomo\Enums;

enum Format: string
{
    case Json = 'json';
    case Xml = 'xml';
    case Csv = 'csv';
    case Tsv = 'tsv';
    case Html = 'html';
    case Rss = 'rss';
}
