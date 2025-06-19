<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationJobResource\Pages;
use App\Filament\Resources\NotificationJobResource\RelationManagers;
use App\Models\NotificationJob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NotificationJobResource extends Resource
{
    protected static ?string $model = NotificationJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('date_sent')
                    ->label('Date & Time')
                    ->seconds(false)
                    ->native(false), // отключает браузерный нативный ввод (если нужно)
                Forms\Components\Textarea::make('text')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date_sent')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('text')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationJobs::route('/'),
            'create' => Pages\CreateNotificationJob::route('/create'),
            'edit' => Pages\EditNotificationJob::route('/{record}/edit'),
        ];
    }
}
