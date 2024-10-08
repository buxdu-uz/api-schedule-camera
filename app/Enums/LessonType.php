<?php

namespace App\Enums;

enum LessonType: string
{
    #enum('lecture', 'practice','seminar' 'laboratory')
    case LECTURE = 'lecture';
    case PRACTICE = 'practice';
    case SEMINAR = 'seminar';
    case LABORATORY = 'laboratory';

    /**
     * @return array[]
     */
    public static function getArray(): array
    {
        return [
            [
                'name' => self::LECTURE->name,
                'value' => self::LECTURE->value,
                'trans' => self::LECTURE->getTextValue()
            ],
            [
                'name' => self::PRACTICE->name,
                'value' => self::PRACTICE->value,
                'trans' => self::PRACTICE->getTextValue()
            ],
            [
                'name' => self::SEMINAR->name,
                'value' => self::SEMINAR->value,
                'trans' => self::SEMINAR->getTextValue()
            ],
            [
                'name' => self::LABORATORY->name,
                'value' => self::LABORATORY->value,
                'trans' => self::LABORATORY->getTextValue()
            ],
        ];
    }

    /**
     * @return string
     */
    public function getTextValue(): string
    {
        return match ($this) {
            self::LECTURE => 'Maruza',
            self::PRACTICE => 'Amaliyot',
            self::SEMINAR => 'Seminar',
            self::LABORATORY => 'Labaratoriya',
        };
    }
}
