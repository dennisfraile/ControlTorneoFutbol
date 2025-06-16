<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Torneo;

class ResetTorneo extends Command
{
    protected $signature = 'torneo:reset {torneo_id}';
    protected $description = 'Limpia todos los datos de un torneo (jugadores, equipos, partidos, estadísticas)';

    public function handle()
    {
        $torneoId = $this->argument('torneo_id');
        $torneo = Torneo::find($torneoId);

        if (!$torneo) {
            $this->error('Torneo no encontrado.');
            return;
        }

        // Eliminar estadísticas
        $torneo->partidos()->each(function ($partido) {
            $partido->estadisticas()->delete();
        });

        // Eliminar partidos
        $torneo->partidos()->delete();

        // Eliminar jugadores y equipos
        $torneo->equipos()->each(function ($equipo) {
            $equipo->jugadores()->delete();
        });

        $torneo->equipos()->delete();

        $this->info("Torneo #{$torneoId} ha sido limpiado exitosamente.");
    }
}
