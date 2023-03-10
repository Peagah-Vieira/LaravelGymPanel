<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers\MembershiptypeRelationManager;
use App\Filament\Resources\PaymentResource\RelationManagers\UserRelationManager;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use HusamTariq\FilamentTimePicker\Forms\Components\TimePickerField;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-cash';

    protected static ?string $navigationLabel = 'Payments';

    protected static ?string $navigationGroup = 'Manager Resources';

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->user->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['user.name', 'amount'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('user_name')
                            ->label('User name')
                            ->relationship('user', 'name')
                            ->disablePlaceholderSelection()
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('type_name')
                            ->label('Membership type')
                            ->relationship('membershiptype', 'type_name')
                            ->disablePlaceholderSelection()
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask->money(prefix: '$', isSigned: false))
                            ->required(),
                        TimePickerField::make('payment_time')
                            ->okLabel('Confirm')
                            ->cancelLabel('Cancel'),
                        Forms\Components\DatePicker::make('payment_date')
                            ->placeholder('Jan 5, 2023')
                            ->maxDate(now())
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('membershiptype.type_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('amount')
                    ->prefix('$')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->searchable()
                    ->sortable()
                    ->date(),
                Tables\Columns\TextColumn::make('payment_time')
                    ->time()
                    ->searchable()
                    ->sortable()
            ])->defaultSort('id')
            ->filters([
                Tables\Filters\Filter::make('payment_date')
                    ->form([
                        Forms\Components\DatePicker::make('payment_date_from')
                            ->placeholder('Jan 30, 2022')
                            ->maxDate(now()),
                        Forms\Components\DatePicker::make('payment_date_until')
                            ->placeholder('Jan 11, 2023')
                            ->maxDate(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['payment_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date), 
                            )
                            ->when(
                                $data['payment_date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date), 
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['payment_date_from'] ?? null) {
                            $indicators['from'] = 'Payment date from ' . Carbon::parse($data['payment_date_from'])->toFormattedDateString();
                        }

                        if ($data['payment_date_until'] ?? null) {
                            $indicators['until'] = 'Payment date until ' . Carbon::parse($data['payment_date_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                Tables\Filters\Filter::make('payment_time')
                    ->form([
                        Forms\Components\TimePicker::make('payment_time_from')
                            ->placeholder('06:00:00'),
                        Forms\Components\TimePicker::make('payment_time_until')
                            ->placeholder('24:00:00'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['payment_time_from'],
                                fn (Builder $query, $time): Builder => $query->whereDate('payment_time', '>=', $time),
                            )
                            ->when(
                                $data['payment_time_until'],
                                fn (Builder $query, $time): Builder => $query->whereDate('payment_time', '<=', $time),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['payment_time_from'] ?? null) {
                            $indicators['from'] = 'Payment time from ' . Carbon::parse($data['payment_time_from'])->toTimeString();
                        }

                        if ($data['payment_time_until'] ?? null) {
                            $indicators['until'] = 'Payment time until ' . Carbon::parse($data['payment_time_until'])->toTimeString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UserRelationManager::class,
            MembershiptypeRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
