<?php

namespace Database\Seeders;

use App\Models\NotificationMessage;
use App\Models\NotificationType;
use Illuminate\Database\Seeder;

class NotificationMessageSeeder extends Seeder
{
    public function run(): void
    {
        $messages = [

            // ══════════════════════════════════════════════════════════════════
            // AGUA — sin vasos registrados   |  Placeholders: {meta}
            // ══════════════════════════════════════════════════════════════════
            ['type' => 'agua', 'context' => 'cero_vasos', 'body' => '¡Recuerda hidratarte! Tu meta es {meta} vasos hoy. 💧'],
            ['type' => 'agua', 'context' => 'cero_vasos', 'body' => 'El agua es combustible para tus músculos. ¡Empieza a hidratarte! Meta: {meta} vasos. 💧'],
            ['type' => 'agua', 'context' => 'cero_vasos', 'body' => '¡Aún no has tomado agua hoy! Tu cuerpo necesita {meta} vasos. ¡Vamos! 💧'],
            ['type' => 'agua', 'context' => 'cero_vasos', 'body' => 'Hidratarse bien mejora tu rendimiento. Hoy necesitas {meta} vasos. ¡Empieza ahora! 💦'],
            ['type' => 'agua', 'context' => 'cero_vasos', 'body' => 'Sin agua no hay energía. ¡Meta de hoy: {meta} vasos! ¿A qué esperas? 💧'],

            // ══════════════════════════════════════════════════════════════════
            // AGUA — en progreso   |  Placeholders: {vasos} {meta} {faltan}
            // ══════════════════════════════════════════════════════════════════
            ['type' => 'agua', 'context' => 'progreso', 'body' => 'Ya llevas {vasos}/{meta} vasos. ¡Te faltan {faltan} más! 💧'],
            ['type' => 'agua', 'context' => 'progreso', 'body' => '¡Vas bien! {vasos} de {meta} vasos completados. ¡Solo te faltan {faltan}! 💪💧'],
            ['type' => 'agua', 'context' => 'progreso', 'body' => 'Llevas {vasos} vasos, te quedan {faltan} para tu meta de hoy. ¡Tú puedes! 💦'],
            ['type' => 'agua', 'context' => 'progreso', 'body' => '{vasos}/{meta} vasos. ¡Solo {faltan} más para completar tu meta de hidratación! 💧🎯'],
            ['type' => 'agua', 'context' => 'progreso', 'body' => '¡A {faltan} vasos de lograrlo! Sigue así, ya llevas {vasos} de {meta}. 💦'],

            // ══════════════════════════════════════════════════════════════════
            // RECORDATORIO — sin racha activa
            // ══════════════════════════════════════════════════════════════════
            ['type' => 'recordatorio', 'context' => 'sin_racha', 'body' => '¡Es hora de entrenar! Un día más cerca de tus metas. 💪'],
            ['type' => 'recordatorio', 'context' => 'sin_racha', 'body' => 'Hoy es un buen día para empezar tu racha. ¡Entrena y suma el primer día! 🔥'],
            ['type' => 'recordatorio', 'context' => 'sin_racha', 'body' => 'El mejor momento para comenzar fue ayer; el segundo mejor es ahora. ¡Muévete! 🚀'],
            ['type' => 'recordatorio', 'context' => 'sin_racha', 'body' => 'Cada entrenamiento cuenta. ¡Haz que este sea el primero de una gran racha! 🏋️'],
            ['type' => 'recordatorio', 'context' => 'sin_racha', 'body' => '¿Listo para entrenar hoy? Tu cuerpo y tus metas te lo agradecerán. 💪'],
            ['type' => 'recordatorio', 'context' => 'sin_racha', 'body' => 'Un día de entrenamiento es un ladrillo en tu mejor versión. ¡Ponlo hoy! 🧱'],

            // ══════════════════════════════════════════════════════════════════
            // RECORDATORIO — con racha activa   |  Placeholders: {racha} {dias}
            // ══════════════════════════════════════════════════════════════════
            ['type' => 'recordatorio', 'context' => 'con_racha', 'body' => '¡No pierdas tu racha de {racha} {dias}! Entrena hoy para mantenerla. 💪'],
            ['type' => 'recordatorio', 'context' => 'con_racha', 'body' => '¡{racha} {dias} de racha y contando! No la rompas hoy. 🔥'],
            ['type' => 'recordatorio', 'context' => 'con_racha', 'body' => 'Llevas {racha} {dias} seguidos entrenando. ¡Hoy no puede ser la excepción! 💥'],
            ['type' => 'recordatorio', 'context' => 'con_racha', 'body' => 'Tu racha de {racha} {dias} te está esperando. ¡Un entrenamiento más y sigue creciendo! 🏆'],
            ['type' => 'recordatorio', 'context' => 'con_racha', 'body' => '¡{racha} {dias} de constancia! No pares ahora, estás construyendo un hábito. 💪🔗'],
            ['type' => 'recordatorio', 'context' => 'con_racha', 'body' => 'Racha actual: {racha} {dias}. ¡Protégela! Un entreno de hoy vale oro. 🥇'],

            // ══════════════════════════════════════════════════════════════════
            // MOTIVACIÓN — mañana
            // ══════════════════════════════════════════════════════════════════
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'Hoy es un día perfecto para entrenar. 🔥 Tu yo del futuro te lo agradecerá.'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'El que madruga, gana músculo. 💪 Empieza el día moviéndote.'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'Cada mañana que entrenas es una victoria sobre ayer. ¡Vamos! 🏋️'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'Tu cuerpo está listo y esperándote. 🌅 Haz que este día cuente.'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'Los grandes cambios empiezan con un pequeño esfuerzo cada mañana. ¡Tú puedes! 🌟'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'La disciplina de hoy es la fuerza de mañana. 💥 ¡Arranca!'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'No esperes la motivación — empieza y ella llegará. 🚀'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'Un entrenamiento hoy es un regalo que te das a ti mismo. 🎁'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'Los campeones se construyen mañana a mañana. ¡Esta es la tuya! 🏆'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'Mueve el cuerpo, despeja la mente. 🧠💪 Empieza bien el día.'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'Cada repetición te acerca más a tu mejor versión. ¡A sumar! 🔑'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'Tu cuerpo puede más de lo que crees. Demuéstraselo hoy. 💫'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'El esfuerzo de la mañana vale doble. ¡No lo dejes pasar! ⚡'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'Levántate, entrena, conquista. El día es tuyo. 🌤️'],
            ['type' => 'motivacion', 'context' => 'manana', 'body' => 'Mientras otros duermen, tú construyes. 💪 ¡Aprovecha la mañana!'],

            // ══════════════════════════════════════════════════════════════════
            // MOTIVACIÓN — noche, entrenó hoy
            // ══════════════════════════════════════════════════════════════════
            ['type' => 'motivacion', 'context' => 'noche_entrenado', 'body' => '¡Increíble esfuerzo hoy! 🏆 Descansa bien, mañana volvemos más fuertes.'],
            ['type' => 'motivacion', 'context' => 'noche_entrenado', 'body' => '¡Lo lograste! 🌟 Cada entrenamiento te acerca más a tu mejor versión.'],
            ['type' => 'motivacion', 'context' => 'noche_entrenado', 'body' => '¡Otro día, otra victoria! 💪 Siéntete orgulloso de lo que conseguiste hoy.'],
            ['type' => 'motivacion', 'context' => 'noche_entrenado', 'body' => 'El trabajo de hoy habla por ti. 🔥 Descansa y recarga energías.'],
            ['type' => 'motivacion', 'context' => 'noche_entrenado', 'body' => '¡Misión cumplida! 🎯 Tu constancia está construyendo resultados reales.'],
            ['type' => 'motivacion', 'context' => 'noche_entrenado', 'body' => 'Siente el orgullo de quien cumplió. 😤 Mañana seguimos.'],
            ['type' => 'motivacion', 'context' => 'noche_entrenado', 'body' => '¡Un día más sumado a tu racha! 📈 Así se forjan los campeones.'],
            ['type' => 'motivacion', 'context' => 'noche_entrenado', 'body' => 'Tu cuerpo agradece el esfuerzo de hoy. 🙏 Buen descanso.'],
            ['type' => 'motivacion', 'context' => 'noche_entrenado', 'body' => 'Hoy demostraste que eres más fuerte que tus excusas. 💥 ¡Sigue así!'],
            ['type' => 'motivacion', 'context' => 'noche_entrenado', 'body' => '¡Excelente sesión! 🌙 El descanso es parte del entrenamiento — úsalo bien.'],

            // ══════════════════════════════════════════════════════════════════
            // MOTIVACIÓN — noche, no entrenó hoy
            // ══════════════════════════════════════════════════════════════════
            ['type' => 'motivacion', 'context' => 'noche_no_entrenado', 'body' => 'El día aún no termina. 🌆 Incluso 20 minutos de ejercicio marcan la diferencia.'],
            ['type' => 'motivacion', 'context' => 'noche_no_entrenado', 'body' => '¡Última llamada para sumar a tu racha! ⏰ Un pequeño esfuerzo vale mucho.'],
            ['type' => 'motivacion', 'context' => 'noche_no_entrenado', 'body' => 'No dejes que el día cierre sin mover el cuerpo. 🔥 ¡Vamos!'],
            ['type' => 'motivacion', 'context' => 'noche_no_entrenado', 'body' => 'Cierra el día con energía. 💪 Una sesión corta es mejor que ninguna.'],
            ['type' => 'motivacion', 'context' => 'noche_no_entrenado', 'body' => 'El cansancio es temporal, el progreso es permanente. 🌟 ¿Qué esperas?'],
            ['type' => 'motivacion', 'context' => 'noche_no_entrenado', 'body' => 'Mañana te alegrarás de haberte movido hoy. 🎯 ¡Anda, 15 minutos bastan!'],
            ['type' => 'motivacion', 'context' => 'noche_no_entrenado', 'body' => 'No te vayas a dormir con la deuda del entrenamiento. 😤 ¡Puedes hacerlo!'],
            ['type' => 'motivacion', 'context' => 'noche_no_entrenado', 'body' => 'Una racha se rompe en un instante y se construye día a día. Protege la tuya. 🔒'],
            ['type' => 'motivacion', 'context' => 'noche_no_entrenado', 'body' => 'El mejor momento para entrenar ya pasó; el segundo mejor es ahora. 🕐'],
            ['type' => 'motivacion', 'context' => 'noche_no_entrenado', 'body' => '¿Qué dirá tu yo de mañana si no te mueves hoy? 🤔 ¡Actúa!'],

            // ══════════════════════════════════════════════════════════════════
            // CUMPLEAÑOS
            // ══════════════════════════════════════════════════════════════════
            ['type' => 'cumpleanos', 'context' => null, 'body' => 'Que este nuevo año de vida esté lleno de metas alcanzadas y récords personales. 🏋️🎂'],
            ['type' => 'cumpleanos', 'context' => null, 'body' => 'El mejor regalo que puedes darte hoy es cuidar tu salud. ¡Que este año alcances todas tus metas! 💪🎉'],
            ['type' => 'cumpleanos', 'context' => null, 'body' => 'Un año más de vida, un año más para superarte. ¡Que tu fuerza crezca tanto como tu felicidad! 🌟🥳'],
            ['type' => 'cumpleanos', 'context' => null, 'body' => 'Hoy es tu día especial. Celebra moviéndote, sudando y siendo tu mejor versión. 🔥🎈'],
            ['type' => 'cumpleanos', 'context' => null, 'body' => 'Un año más, más sabio, más fuerte, más tú. 🏆 ¡Feliz cumple, campeón!'],
            ['type' => 'cumpleanos', 'context' => null, 'body' => 'Que cada año sumes músculo, salud y felicidad. ¡Este año lo vas a romper! 💥🎂'],
            ['type' => 'cumpleanos', 'context' => null, 'body' => 'El tiempo pasa, pero tu determinación crece. ¡Felicidades y a seguir entrenando! 🌠'],
            ['type' => 'cumpleanos', 'context' => null, 'body' => 'Hoy celebras un año más de vida. ¡Que también sea un año más de progreso! 📈🎉'],
            ['type' => 'cumpleanos', 'context' => null, 'body' => 'Los mejores regalos de cumpleaños son salud, fuerza y disciplina. ¡Tú ya los tienes! 💎🎂'],
            ['type' => 'cumpleanos', 'context' => null, 'body' => 'Otro año completado, otro nivel desbloqueado. 🎮💪 ¡Feliz cumpleaños!'],
        ];

        foreach ($messages as $msg) {
            $typeId = NotificationType::where('type', $msg['type'])
                ->where('context', $msg['context'])
                ->value('id');

            if (!$typeId) {
                continue;
            }

            NotificationMessage::firstOrCreate(
                ['notification_type_id' => $typeId, 'body' => $msg['body']],
                ['is_active' => true]
            );
        }
    }
}
