<?php

declare(strict_types=1);

namespace App\System;

use Tempest\Support\IsEnumHelper;

enum Language: string
{
    // @phpstan-ignore-next-line
    use IsEnumHelper;

    case aa = 'Afar';
    case ab = 'Abkhazian';
    case ae = 'Avestan';
    case af = 'Afrikaans';
    case ak = 'Akan';
    case am = 'Amharic';
    case an = 'Aragonese';
    case ar = 'Arabic';
    case as = 'Assamese';
    case av = 'Avaric';
    case ay = 'Aymara';
    case az = 'Azerbaijani';
    case ba = 'Bashkir';
    case be = 'Belarusian';
    case bg = 'Bulgarian';
    case bi = 'Bislama';
    case bm = 'Bambara';
    case bn = 'Bengali';
    case bo = 'Tibetan';
    case br = 'Breton';
    case bs = 'Bosnian';
    case ca = 'Catalan';
    case ce = 'Chechen';
    case ch = 'Chamorro';
    case co = 'Corsican';
    case cr = 'Cree';
    case cs = 'Czech';
    case cu = 'Church Slavic';
    case cv = 'Chuvash';
    case cy = 'Welsh';
    case da = 'Danish';
    case de = 'German';
    case dv = 'Divehi';
    case dz = 'Dzongkha';
    case ee = 'Ewe';
    case el = 'Greek';
    case en = 'English';
    case eo = 'Esperanto';
    case es = 'Spanish';
    case et = 'Estonian';
    case eu = 'Basque';
    case fa = 'Persian';
    case ff = 'Fulah';
    case fi = 'Finnish';
    case fj = 'Fijian';
    case fo = 'Faroese';
    case fr = 'French';
    case fy = 'Western Frisian';
    case ga = 'Irish';
    case gd = 'Gaelic';
    case gl = 'Galician';
    case gn = 'Guarani';
    case gu = 'Gujarati';
    case gv = 'Manx';
    case ha = 'Hausa';
    case he = 'Hebrew';
    case hi = 'Hindi';
    case ho = 'Hiri Motu';
    case hr = 'Croatian';
    case ht = 'Haitian';
    case hu = 'Hungarian';
    case hy = 'Armenian';
    case hz = 'Herero';
    case ia = 'Interlingua';
    case id = 'Indonesian';
    case ie = 'Interlingue';
    case ig = 'Igbo';
    case ii = 'Sichuan Yi';
    case ik = 'Inupiaq';
    case io = 'Ido';
    case is = 'Icelandic';
    case it = 'Italian';
    case iu = 'Inuktitut';
    case ja = 'Japanese';
    case jv = 'Javanese';
    case ka = 'Georgian';
    case kg = 'Kongo';
    case ki = 'Kikuyu';
    case kj = 'Kuanyama';
    case kk = 'Kazakh';
    case kl = 'Kalaallisut';
    case km = 'Central Khmer';
    case kn = 'Kannada';
    case ko = 'Korean';
    case kr = 'Kanuri';
    case ks = 'Kashmiri';
    case ku = 'Kurdish';
    case kv = 'Komi';
    case kw = 'Cornish';
    case ky = 'Kirghiz';
    case la = 'Latin';
    case lb = 'Luxembourgish';
    case lg = 'Ganda';
    case li = 'Limburgan';
    case ln = 'Lingala';
    case lo = 'Lao';
    case lt = 'Lithuanian';
    case lu = 'Luba-Katanga';
    case lv = 'Latvian';
    case mg = 'Malagasy';
    case mh = 'Marshallese';
    case mi = 'Maori';
    case mk = 'Macedonian';
    case ml = 'Malayalam';
    case mn = 'Mongolian';
    case mr = 'Marathi';
    case ms = 'Malay';
    case mt = 'Maltese';
    case my = 'Burmese';
    case na = 'Nauru';
    case nb = 'Norwegian Bokmål';
    case nd = 'Ndebele';
    case ne = 'Nepali';
    case ng = 'Ndonga';
    case nl = 'Dutch';
    case nn = 'Norwegian Nynorsk';
    case no = 'Norwegian';
    case nv = 'Navajo';
    case ny = 'Chichewa';
    case oc = 'Occitan';
    case oj = 'Ojibwa';
    case om = 'Oromo';
    case or = 'Oriya';
    case os = 'Ossetian';
    case pa = 'Panjabi';
    case pi = 'Pali';
    case pl = 'Polish';
    case ps = 'Pushto';
    case pt = 'Portuguese';
    case qu = 'Quechua';
    case rm = 'Romansh';
    case rn = 'Rundi';
    case ro = 'Romanian';
    case ru = 'Russian';
    case rw = 'Kinyarwanda';
    case sa = 'Sanskrit';
    case sc = 'Sardinian';
    case sd = 'Sindhi';
    case se = 'Northern Sami';
    case sg = 'Sango';
    case si = 'Sinhala';
    case sk = 'Slovak';
    case sl = 'Slovenian';
    case sm = 'Samoan';
    case sn = 'Shona';
    case so = 'Somali';
    case sq = 'Albanian';
    case sr = 'Serbian';
    case ss = 'Swati';
    case st = 'Sotho';
    case su = 'Sundanese';
    case sv = 'Swedish';
    case sw = 'Swahili';
    case ta = 'Tamil';
    case te = 'Telugu';
    case tg = 'Tajik';
    case th = 'Thai';
    case ti = 'Tigrinya';
    case tk = 'Turkmen';
    case tl = 'Tagalog';
    case tn = 'Tswana';
    case to = 'Tonga';
    case tr = 'Turkish';
    case ts = 'Tsonga';
    case tt = 'Tatar';
    case tw = 'Twi';
    case ty = 'Tahitian';
    case ug = 'Uighur';
    case uk = 'Ukrainian';
    case ur = 'Urdu';
    case uz = 'Uzbek';
    case ve = 'Venda';
    case vi = 'Vietnamese';
    case vo = 'Volapük';
    case wa = 'Walloon';
    case wo = 'Wolof';
    case xh = 'Xhosa';
    case yi = 'Yiddish';
    case yo = 'Yoruba';
    case za = 'Zhuang';
    case zh = 'Chinese';
    case zu = 'Zulu';

    public static function fromAny(string $name): self
    {
        return self::fromName(strtolower($name));
    }

    public static function tryFromAny(?string $name): ?self
    {
        if ($name === null) {
            return null;
        }

        return self::tryFromName(strtolower($name));
    }

    /**
     * @param string[] $names
     *
     * @return Language[]
     */
    public static function fromAnyArray(array $names): array
    {
        $cases = [];

        foreach ($names as $name) {
            $cases[] = self::fromAny($name);
        }

        return $cases;
    }

    public function lower(): string
    {
        return \strtolower($this->name);
    }

    public function upper(): string
    {
        return \strtoupper($this->name);
    }

    public function titleLower(): string
    {
        return \strtolower($this->value);
    }

    /**
     * @return Language[]
     */
    public static function alphabetically(): array
    {
        $languages = self::cases();

        usort($languages, function (self $a, self $b) {
            return strcasecmp($a->value, $b->value);
        });

        return $languages;
    }
}
