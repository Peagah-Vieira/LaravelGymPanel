<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MembershiptypeResource\Pages;
use App\Filament\Resources\MembershiptypeResource\RelationManagers;
use App\Models\Membershiptype;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MembershiptypeResource extends Resource
{
    protected static ?string $model = Membershiptype::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Membership Resource';

    protected static ?string $navigationGroup = 'Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type_name')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('membership_period')
                    ->required()
                    ->maxLength(15),
                Forms\Components\TextInput::make('membership_amout')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type_name'),
                Tables\Columns\TextColumn::make('membership_period'),
                Tables\Columns\TextColumn::make('membership_amout'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMembershiptypes::route('/'),
            'create' => Pages\CreateMembershiptype::route('/create'),
            'edit' => Pages\EditMembershiptype::route('/{record}/edit'),
        ];
    }    
}
