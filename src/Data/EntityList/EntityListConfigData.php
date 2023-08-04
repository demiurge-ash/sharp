<?php

namespace Code16\Sharp\Data\EntityList;

use Code16\Sharp\Data\CommandData;
use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\EntityStateData;
use Code16\Sharp\Data\Filters\FilterData;
use Code16\Sharp\Data\PageAlertConfigData;
use Code16\Sharp\Enums\CommandType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\RecordTypeScriptType;

final class EntityListConfigData extends Data
{
    public function __construct(
        public string $instanceIdAttribute,
        public bool $searchable,
        public bool $paginated,
        public bool $reorderable,
        public ?string $defaultSort,
        public ?string $defaultSortDir,
        public bool $hasShowPage,
        public string $deleteConfirmationText,
        public bool $deleteHidden,
        /** @var array<DataCollection<FilterData>> */
        #[Optional]
        public ?array $filters = null,
        #[Optional]
        #[RecordTypeScriptType(CommandType::class, 'array<DataCollection<'.CommandData::class.'>>')]
        public ?array $commands = null,
        #[Optional]
        public ?string $multiformAttribute = null,
        #[Optional]
        public ?EntityStateData $state = null,
        #[Optional]
        public ?PageAlertConfigData $globalMessage = null,
    ) {
    }

    public static function from(array $config)
    {
        $config = [
            ...$config,
            'state' => isset($config['state'])
                ? EntityStateData::from($config['state'])
                : null,
            'commands' => isset($config['commands'])
                ? collect($config['commands'])
                    ->map(fn (array $commands) => collect($commands)
                        ->map(fn (array $commands) => CommandData::collection($commands))
                    )
                    ->all()
                : null,
            'filters' => isset($config['filters'])
                ? collect($config['filters'])
                    ->map(fn (array $filters) => FilterData::collection($filters))
                    ->all()
                : null,
            'globalMessage' => isset($config['globalMessage'])
                ? PageAlertConfigData::from($config['globalMessage'])
                : null,
        ];

        return new self(...$config);
    }
}
