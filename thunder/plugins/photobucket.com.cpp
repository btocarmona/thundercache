/* 
 * (c) Copyright 2009 Joaquim Pedro Fran�a Sim�o (osmano807) <osmano807@gmail.com>. All Rights Reserved. 
 * @autor Joaquim Pedro Fran�a Sim�o (osmano807) <osmano807@gmail.com> 
 */

#include <iostream>
#include <cstring>
#include <vector>
#include "../utils.cpp"

// use this line to compile
// g++ -I. -fPIC -shared -g -o photobucket.com.so photobucket.com.cpp

string get_filename(string url) {
		vector<string> resultado;
		if (url.find("?") != string::npos) {
			stringexplode(url, "?", &resultado);
			stringexplode(resultado.at(resultado.size()-2), "/", &resultado);
			return resultado.at(resultado.size()-4) + "_" + resultado.at(resultado.size()-3) + "_" + resultado.at(resultado.size()-2) + "_" +resultado.at(resultado.size()-1);           
		} else {
			stringexplode(url, "/", &resultado);
			return resultado.at(resultado.size()-4) + "_" + resultado.at(resultado.size()-3) + "_" + resultado.at(resultado.size()-2) + "_" +resultado.at(resultado.size()-1); ;
		}
}

extern "C" resposta getmatch(const string url) {
    resposta r;	
	
    r.file = get_filename(url);
	if (!r.file.empty()) {
		r.match = true;
		r.domain = "photobucket";
	} else {
		r.match = false;
	}
	return r;
}
