<?php

namespace packages\actionMusersettings\themes\uikit\Components;
use Bootstrap\Components\BootstrapComponent;

trait getComponentBirthYear {

    /**
     * @param $content string, no support for line feeds
     * @param array $styles 'margin', 'padding', 'orientation', 'background', 'alignment', 'radius', 'opacity',
     * 'orientation', 'height', 'width', 'align', 'crop', 'text-style', 'font-size', 'text-color', 'border-color',
     * 'border-width', 'font-android', 'font-ios', 'background-color', 'background-image', 'background-size',
     * 'color', 'shadow-color', 'shadow-offset', 'shadow-radius', 'vertical-align', 'border-radius', 'text-align',
     * 'lazy', 'floating' (1), 'float' (right | left), 'max-height', 'white-space' (no-wrap), parent_style
     * @param array $parameters selected_state, variable, onclick, style
     * @return \stdClass
     */

    public function getComponentBirthYear(array $parameters=array(),array $styles=array()) {
        /** @var BootstrapView $this */

        $years = '1910;1910;1911;1911;1912;1912;1913;1913;1914;1914;1915;1915;1916;1916;1917;1917;1918;1918;1919;1919;1920;1920;1921;1921;1922;1922;1923;1923;1924;1924;1925;1925;1926;1926;1927;1927;1928;1928;1929;1929;1930;1930;1931;1931;1932;1932;1933;1933;1934;1934;1935;1935;1936;1936;1937;1937;1938;1938;1939;1939;1940;1940;1941;1941;1942;1942;1943;1943;1944;1944;1945;1945;1946;1946;1947;1947;1948;1948;1949;1949;1950;1950;1951;1951;1952;1952;1953;1953;1954;1954;1955;1955;1956;1956;1957;1957;1958;1958;1959;1959;1960;1960;1961;1961;1962;1962;1963;1963;1964;1964;1965;1965;1966;1966;1967;1967;1968;1968;1969;1969;1970;1970;1971;1971;1972;1972;1973;1973;1974;1974;1975;1975;1976;1976;1977;1977;1978;1978;1979;1979;1980;1980;1981;1981;1982;1982;1983;1983;1984;1984;1985;1985;1986;1986;1987;1987;1988;1988;1989;1989;1990;1990;1991;1991;1992;1992;1993;1993;1994;1994;1995;1995;1996;1996;1997;1997;1998;1998;1999;1999;2000;2000;2001;2001;2002;2002;2003;2003;2004;2004;2005;2005;2006;2006;2007;2007;2008;2008';

        $yearvalue = trim($this->model->getSavedVariable('birth_year'));

        $col[] = $this->getComponentFormFieldSelectorList(
            $years,
            array('value' => $yearvalue,'variable' => 'birth_year'),
            array(
                'width' => '100%',
                'height' => '100',
                'margin' => '0 0 0 0',
                'color' => '#FFFFFF',
                'font-size' => '14',
                "background-color" => "#2d2d2d"));

        return $this->getComponentRow($col, array(), array('text-align' => 'center'));
	}

}