<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProduitResource\Pages;
use App\Models\Produit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProduitResource extends Resource
{
    protected static ?string $model = Produit::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Gestion des Produits';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nom')
                    ->required()
                    ->maxLength(255)
                    ->label('Nom du produit'),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->label('Description'),
                Forms\Components\TextInput::make('prix')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->label('Prix unitaire'),
                Forms\Components\TextInput::make('quantite_stock')
                    ->required()
                    ->numeric()
                    ->label('Quantité en stock'),
                Forms\Components\TextInput::make('seuil_alerte')
                    ->required()
                    ->numeric()
                    ->label('Seuil d\'alerte'),
                Forms\Components\Select::make('categorie_id')
                    ->relationship('categorie', 'nom')
                    ->required()
                    ->label('Catégorie'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prix')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantite_stock')
                    ->numeric()
                    ->sortable()
                    ->color(fn (int $state): string => $state < 10 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('categorie.nom')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListProduits::route('/'),
            'create' => Pages\CreateProduit::route('/create'),
            'edit' => Pages\EditProduit::route('/{record}/edit'),
        ];
    }
}