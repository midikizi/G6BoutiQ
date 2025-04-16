<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MouvementStockResource\Pages;
use App\Models\MouvementStock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MouvementStockResource extends Resource
{
    protected static ?string $model = MouvementStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Gestion des Stocks';

    protected static ?string $navigationLabel = 'Mouvements de Stock';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('produit_id')
                    ->relationship('produit', 'nom')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('type_mouvement')
                    ->options([
                        'entree' => 'Entrée',
                        'sortie' => 'Sortie',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('quantite')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('motif')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference_document')
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('date_mouvement')
                    ->required()
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('produit.nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type_mouvement')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'entree' => 'success',
                        'sortie' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('quantite')
                    ->sortable(),
                Tables\Columns\TextColumn::make('motif')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reference_document')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_mouvement')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('date_mouvement', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type_mouvement')
                    ->options([
                        'entree' => 'Entrée',
                        'sortie' => 'Sortie',
                    ]),
                Tables\Filters\SelectFilter::make('produit')
                    ->relationship('produit', 'nom'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListMouvementStocks::route('/'),
            'create' => Pages\CreateMouvementStock::route('/create'),
            'view' => Pages\ViewMouvementStock::route('/{record}'),
        ];
    }
}