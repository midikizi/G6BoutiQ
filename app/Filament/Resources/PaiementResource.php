<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaiementResource\Pages;
use App\Models\Paiement;
use App\Models\Vente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaiementResource extends Resource
{
    protected static ?string $model = Paiement::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Ventes';

    protected static ?string $navigationLabel = 'Paiements';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Paiement';

    protected static ?string $pluralModelLabel = 'Paiements';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('vente_id')
                    ->relationship('vente', 'numero_facture', function (Builder $query) {
                        return $query->where('statut', 'en_cours');
                    })
                    ->required()
                    ->default(request()->query('vente_id'))
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, callable $set) {
                        if ($vente_id = $get('vente_id')) {
                            $vente = Vente::find($vente_id);
                            if ($vente) {
                                $set('montant', $vente->montant_total);
                            }
                        }
                    })
                    ->label('Vente'),
                Forms\Components\TextInput::make('montant')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->default(function (Get $get) {
                        if ($vente_id = $get('vente_id')) {
                            $vente = Vente::find($vente_id);
                            return $vente ? $vente->montant_total : null;
                        }
                        return null;
                    })
                    ->label('Montant'),
                Forms\Components\Select::make('mode_paiement')
                    ->options([
                        'especes' => 'Espèces',
                        'carte' => 'Carte',
                        'virement' => 'Virement',
                    ])
                    ->required()
                    ->default('especes')
                    ->label('Mode de paiement'),
                Forms\Components\Select::make('statut')
                    ->options([
                        'en_attente' => 'En attente',
                        'valide' => 'Validé',
                        'refuse' => 'Refusé',
                    ])
                    ->required()
                    ->default('en_attente')
                    ->label('Statut'),
                Forms\Components\DateTimePicker::make('date_paiement')
                    ->required()
                    ->default(now())
                    ->label('Date de paiement'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vente.numero_facture')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mode_paiement')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'especes' => 'gray',
                        'carte' => 'blue',
                        'virement' => 'green',
                    }),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'en_attente' => 'warning',
                        'valide' => 'success',
                        'refuse' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('date_paiement')
                    ->dateTime()
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
            'index' => Pages\ListPaiements::route('/'),
            'create' => Pages\CreatePaiement::route('/create'),
            'edit' => Pages\EditPaiement::route('/{record}/edit'),
        ];
    }
}