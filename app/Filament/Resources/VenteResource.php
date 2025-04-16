<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VenteResource\Pages;
use App\Models\Vente;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Produit;

class VenteResource extends Resource
{
    protected static ?string $model = Vente::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationGroup = 'Ventes';

    protected static ?string $navigationLabel = 'Nouvelle Vente';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Vente';

    protected static ?string $pluralModelLabel = 'Ventes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Client')
                        ->schema([
                            Forms\Components\Select::make('client_id')
                                ->relationship('client', 'nom')
                                ->required()
                                ->label('Client')
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('nom')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('prenom')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('telephone')
                                        ->tel()
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('adresse')
                                        ->required()
                                        ->maxLength(65535),
                                ]),
                        ])
                        ->icon('heroicon-o-user'),
                    Step::make('Informations de vente')
                        ->schema([
                            Forms\Components\DateTimePicker::make('date_vente')
                                ->required()
                                ->default(now())
                                ->label('Date de vente'),
                            Forms\Components\TextInput::make('montant_total')
                                ->numeric()
                                ->prefix('€')
                                ->default(0)
                                ->disabled()
                                ->label('Montant total (calculé automatiquement)')
                                ->helperText('Le montant total sera calculé automatiquement après l\'ajout des lignes de vente'),
                            Forms\Components\Select::make('statut')
                                ->options([
                                    'en_cours' => 'En cours',
                                    'payee' => 'Payée',
                                    'annulee' => 'Annulée',
                                ])
                                ->required()
                                ->default('en_cours')
                                ->label('Statut'),
                        ])
                        ->icon('heroicon-o-shopping-cart'),
                    Step::make('Lignes de vente')
                        ->schema([
                            Forms\Components\Repeater::make('ligneVentes')
                                ->relationship()
                                ->schema([
                                    Forms\Components\Select::make('produit_id')
                                        ->relationship('produit', 'nom')
                                        ->required()
                                        ->label('Produit')
                                        ->searchable()
                                        ->preload()
                                        ->live()
                                        ->afterStateUpdated(function (Get $get, Set $set, $livewire) {
                                            $produit = Produit::find($get('produit_id'));
                                            if ($produit) {
                                                $set('prix_unitaire', $produit->prix);
                                                $quantite = $get('quantite') ?? 1;
                                                $montant = $produit->prix * $quantite;
                                                $set('montant_total', $montant);
                                                
                                                // Mettre à jour le montant total de la vente
                                                $total = collect($livewire->data['ligneVentes'] ?? [])->sum('montant_total');
                                                $livewire->data['montant_total'] = $total;
                                            }
                                        }),
                                    Forms\Components\TextInput::make('quantite')
                                        ->required()
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->label('Quantité')
                                        ->live()
                                        ->afterStateUpdated(function (Get $get, Set $set, $livewire) {
                                            $quantite = $get('quantite') ?? 1;
                                            $prix_unitaire = $get('prix_unitaire') ?? 0;
                                            $montant = $prix_unitaire * $quantite;
                                            $set('montant_total', $montant);
                                            
                                            // Mettre à jour le montant total de la vente
                                            $total = collect($livewire->data['ligneVentes'] ?? [])->sum('montant_total');
                                            $livewire->data['montant_total'] = $total;
                                        }),
                                    Forms\Components\TextInput::make('prix_unitaire')
                                        ->required()
                                        ->numeric()
                                        ->default(0)
                                        ->minValue(0)
                                        ->prefix('€')
                                        ->label('Prix unitaire')
                                        ->live()
                                        ->afterStateUpdated(function (Get $get, Set $set, $livewire) {
                                            $quantite = $get('quantite') ?? 1;
                                            $prix_unitaire = $get('prix_unitaire') ?? 0;
                                            $montant = $prix_unitaire * $quantite;
                                            $set('montant_total', $montant);
                                            
                                            // Mettre à jour le montant total de la vente
                                            $total = collect($livewire->data['ligneVentes'] ?? [])->sum('montant_total');
                                            $livewire->data['montant_total'] = $total;
                                        }),
                                    Forms\Components\TextInput::make('montant_total')
                                        ->required()
                                        ->numeric()
                                        ->default(0)
                                        ->prefix('€')
                                        ->label('Montant total')
                                        ->disabled(),
                                ])
                                ->columns(4)
                                ->defaultItems(1)
                                ->minItems(1)
                                ->collapsible()
                                ->itemLabel(fn (array $state): ?string => $state['produit_id'] ?? null)
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if (is_array($state)) {
                                        $montantTotal = collect($state)->sum('montant_total');
                                        $set('montant_total', $montantTotal);
                                    }
                                }),
                        ])
                        ->icon('heroicon-o-rectangle-stack'),
                    Step::make('Paiement')
                        ->schema([
                            Forms\Components\TextInput::make('montant_total')
                                ->required()
                                ->numeric()
                                ->prefix('€')
                                ->disabled()
                                ->label('Montant à payer'),
                            Forms\Components\Select::make('mode_paiement')
                                ->options([
                                    'especes' => 'Espèces',
                                    'carte' => 'Carte',
                                    'virement' => 'Virement',
                                ])
                                ->required()
                                ->default('especes')
                                ->label('Mode de paiement'),
                            Forms\Components\Select::make('statut_paiement')
                                ->options([
                                    'en_attente' => 'En attente',
                                    'valide' => 'Validé',
                                    'refuse' => 'Refusé',
                                ])
                                ->required()
                                ->default('en_attente')
                                ->label('Statut du paiement'),
                            Forms\Components\DateTimePicker::make('date_paiement')
                                ->required()
                                ->default(now())
                                ->label('Date de paiement'),
                        ])
                        ->icon('heroicon-o-credit-card'),
                ])
                ->columnSpanFull()
                ->persistStepInQueryString()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_facture')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.nom')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_vente')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('montant_total')
                    ->money('EUR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'en_cours' => 'warning',
                        'payee' => 'success',
                        'annulee' => 'danger',
                    }),
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
            'index' => Pages\ListVentes::route('/'),
            'create' => Pages\CreateVente::route('/create'),
            'edit' => Pages\EditVente::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('statut', 'en_cours')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('statut', 'en_cours')->count() > 0 ? 'warning' : 'success';
    }
}