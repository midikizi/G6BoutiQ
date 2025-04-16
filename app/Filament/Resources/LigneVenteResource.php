<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LigneVenteResource\Pages;
use App\Models\LigneVente;
use App\Models\Produit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LigneVenteResource extends Resource
{
    protected static ?string $model = LigneVente::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Ventes';

    protected static ?string $navigationLabel = 'Lignes de Vente';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Ligne de Vente';

    protected static ?string $pluralModelLabel = 'Lignes de Vente';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('vente_id')
                    ->relationship('vente', 'numero_facture')
                    ->required()
                    ->label('Vente'),
                Forms\Components\Select::make('produit_id')
                    ->relationship('produit', 'nom')
                    ->required()
                    ->label('Produit')
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $produit = Produit::find($get('produit_id'));
                        if ($produit) {
                            $set('prix_unitaire', $produit->prix);
                            $quantite = $get('quantite') ?? 0;
                            $set('montant_total', $produit->prix * $quantite);
                        }
                    }),
                Forms\Components\TextInput::make('quantite')
                    ->required()
                    ->numeric()
                    ->label('Quantité')
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $quantite = $get('quantite') ?? 0;
                        $prix_unitaire = $get('prix_unitaire') ?? 0;
                        $set('montant_total', $prix_unitaire * $quantite);
                    }),
                Forms\Components\TextInput::make('prix_unitaire')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->label('Prix unitaire')
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $quantite = $get('quantite') ?? 0;
                        $prix_unitaire = $get('prix_unitaire') ?? 0;
                        $set('montant_total', $prix_unitaire * $quantite);
                    }),
                Forms\Components\TextInput::make('montant_total')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->label('Montant total')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vente.numero_facture')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('produit.nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantite')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prix_unitaire')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_total')
                    ->money('EUR')
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
            'index' => Pages\ListLigneVentes::route('/'),
            'create' => Pages\CreateLigneVente::route('/create'),
            'edit' => Pages\EditLigneVente::route('/{record}/edit'),
        ];
    }
}