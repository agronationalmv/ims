<?php

namespace App\Reports\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Tables\Filters\Filter;
use Livewire\Component;

abstract class ReportContract  implements FromCollection, WithHeadings{
    use Exportable;

    protected static string $title;

    public static array $filters=[];

    public static abstract function getColumns();
    public static abstract function query();
    
    public function headings(): array{
        return array_keys(static::getColumns());
    }

    public function collection(){
        return static::filter(static::query(),static::$filters)->get()->map(fn($item)=>$this->mapColumn($item));
    }

    public static function tableFilters():mixed{
        if(static::filterForm()){
            return [Filter::make('filters')
                        ->form(fn (Component $livewire) => $livewire->report->provider->filterForm())
                        ->query(fn (Builder $query, array $data,Component $livewire) => $livewire->report->provider->filter($query,$data))];
        }
        return [];

    }
    public static function filterForm():array{
        return [];
    }
    public static function filter(Builder $query,array $data): Builder{
        return $query;
    }

    public function evaluateColumn($col,$row){
        if(method_exists($this,$col)){
            return $this->{$col}($row);
        }elseif(gettype($col)=='string'){
            $parts=explode('.',$col);
            
            $val=$row;
            foreach($parts as $key){
                $val=$val->{$key};
            }
            return $val;
        }
        return null;
    }

    public function mapColumn($row){
        $map=[];
        foreach(static::getColumns() as $title=>$col){
            $map[$title]=$this->evaluateColumn($col,$row);
        }
        return $map;
    }

    public function export($filters){
        static::$filters=$filters;
        return Excel::download(new $this, static::$title.time().".xlsx");
    }
}