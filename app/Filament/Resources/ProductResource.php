<?php

namespace App\Filament\Resources;


use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\SpatieTagsInput;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    SpatieMediaLibraryFileUpload::make('cover')
                        ->collection('cover'),
                    SpatieMediaLibraryFileUpload::make('gallery')
                        ->collection('gallery')
                        ->multiple(),
                    TextInput::make('name')
                        ->label("Product Name"),
                    TextInput::make('sku')
                        ->label("SKU")
                        ->unique(ignoreRecord: true),
                    TextInput::make('slug')
                        ->unique(ignoreRecord: true),
                    SpatieTagsInput::make('tags')
                        ->type('collection')
                        ->label('Collection'),
                    TextInput::make('stock')
                        ->numeric()
                        ->default(0),
                    TextInput::make('price')
                        ->numeric()
                        ->prefix('Rp'),
                    TextInput::make('weight')
                        ->numeric()
                        ->suffix('gram'),
                    MarkdownEditor::make('description')
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
