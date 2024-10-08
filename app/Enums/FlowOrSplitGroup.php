<?php

namespace App\Enums;

enum FlowOrSplitGroup: string
{
    #enum(true, false)
    case YES = 'yes';
    case NO = 'no';

    /**
     * @return array[]
     */
    public static function getArray(): array
    {
        return [
            [
                'name' => self::YES->name,
                'value' => self::YES->value,
                'trans' => self::YES->getTextValue()
            ],
            [
                'name' => self::NO->name,
                'value' => self::NO->value,
                'trans' => self::NO->getTextValue()
            ]
        ];
    }

    /**
     * @return string
     */
    public function getTextValue(): string
    {
        return match ($this) {
            self::YES => 'Ha',
            self::NO => 'Yo\'q'
        };
    }
}
