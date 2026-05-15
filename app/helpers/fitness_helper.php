<?php
// funciones de apoyo para calculos fitness

function fitness_activity_options(): array
{
    return range(1, 7);
}

function fitness_intensity_options(): array
{
    return [
        "baja" => "Baja",
        "media" => "Media",
        "alta" => "Alta"
    ];
}

function fitness_goal_options(): array
{
    return [
        "perder_grasa" => "Perder grasa",
        "mantener_peso" => "Mantener peso",
        "ganar_masa" => "Ganar masa muscular"
    ];
}

function calculate_fitness(array $datos): array
{
    $peso = (float) ($datos["peso"] ?? 0);
    $altura = (float) ($datos["altura"] ?? 0);
    $edad = (int) ($datos["edad"] ?? 0);
    $sexo = (string) ($datos["sexo"] ?? "hombre");
    $actividad = (int) ($datos["actividad_semanal"] ?? 1);
    $intensidad = (string) ($datos["intensidad"] ?? "media");
    $objetivo = (string) ($datos["objetivo"] ?? "mantener_peso");

    $factor_actividad = [
        1 => 1.20,
        2 => 1.28,
        3 => 1.36,
        4 => 1.44,
        5 => 1.53,
        6 => 1.62,
        7 => 1.70
    ];
    $factor_intensidad = [
        "baja" => 0.98,
        "media" => 1.00,
        "alta" => 1.08
    ];
    $ajuste_objetivo = [
        "perder_grasa" => -350,
        "mantener_peso" => 0,
        "ganar_masa" => 300
    ];
    $proteinas_por_kg = [
        "perder_grasa" => 2.0,
        "mantener_peso" => 1.8,
        "ganar_masa" => 2.0
    ];
    $grasas_por_kg = [
        "perder_grasa" => 0.8,
        "mantener_peso" => 0.9,
        "ganar_masa" => 1.0
    ];
    $agua_extra = [
        "baja" => 0.00,
        "media" => 0.20,
        "alta" => 0.40
    ];

    $altura_m = $altura > 0 ? $altura / 100 : 0;
    $imc = $altura_m > 0 ? $peso / ($altura_m * $altura_m) : 0;
    $tmb = $sexo === "mujer"
        ? (10 * $peso) + (6.25 * $altura) - (5 * $edad) - 161
        : (10 * $peso) + (6.25 * $altura) - (5 * $edad) + 5;

    $mantenimiento = $tmb * ($factor_actividad[$actividad] ?? 1.2) * ($factor_intensidad[$intensidad] ?? 1);
    $calorias = (int) round($mantenimiento + ($ajuste_objetivo[$objetivo] ?? 0));
    $proteinas = round($peso * ($proteinas_por_kg[$objetivo] ?? 1.8), 1);
    $grasas = round($peso * ($grasas_por_kg[$objetivo] ?? 0.9), 1);
    $kcal_restantes = max($calorias - (($proteinas * 4) + ($grasas * 9)), 0);
    $carbohidratos = round($kcal_restantes / 4, 1);
    $agua_litros = round(($peso * 0.033) + ($actividad * 0.10) + ($agua_extra[$intensidad] ?? 0), 2);

    return [
        "imc" => round($imc, 2),
        "calorias_recomendadas" => $calorias,
        "agua_litros" => $agua_litros,
        "proteinas" => $proteinas,
        "carbohidratos" => $carbohidratos,
        "grasas" => $grasas
    ];
}

