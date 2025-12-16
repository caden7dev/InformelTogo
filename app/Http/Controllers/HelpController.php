<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessage;

class HelpController extends Controller
{
    public function support()
    {
        return view('help.support');
    }

    public function faq()
    {
        $faqs = [
            [
                'question' => 'Comment ajouter une transaction ?',
                'answer' => 'Cliquez sur "Nouvelle Transaction" dans le tableau de bord, remplissez les informations et sauvegardez.'
            ],
            [
                'question' => 'Comment créer un budget ?',
                'answer' => 'Allez dans la section "Budgets", cliquez sur "Nouveau Budget", définissez le montant et la catégorie.'
            ],
            [
                'question' => 'Comment exporter mes données ?',
                'answer' => 'Utilisez la fonction d\'exportation dans la section "Rapports" pour exporter en CSV, Excel ou PDF.'
            ],
            [
                'question' => 'Comment définir des objectifs ?',
                'answer' => 'Allez dans la section "Objectifs" pour définir vos objectifs d\'épargne et suivre votre progression.'
            ],
            [
                'question' => 'Comment gérer les notifications ?',
                'answer' => 'Configurez vos préférences de notifications dans "Paramètres" > "Notifications".'
            ]
        ];

        return view('help.faq', compact('faqs'));
    }

    public function contact()
    {
        return view('help.contact');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10'
        ]);

        // Envoyer l'email (à configurer dans .env)
        // Mail::to(config('mail.support_address'))->send(new ContactMessage($request->all()));

        return redirect()->route('help.contact')
            ->with('success', 'Votre message a été envoyé avec succès !');
    }

    public function documentation()
    {
        $sections = [
            'guide_demarrage' => 'Guide de démarrage',
            'transactions' => 'Gestion des transactions',
            'budgets' => 'Gestion des budgets',
            'rapports' => 'Génération de rapports',
            'notifications' => 'Système de notifications'
        ];

        return view('help.documentation', compact('sections'));
    }
}