import {  StyleSheet, Text, View, Image, TouchableOpacity } from 'react-native'
import React from 'react'
import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { MaterialIcons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

const Configuracao = ({ navigation }) => {
    const logout = () => {
        AsyncStorage.removeItem('user').then(() => {
            navigation.navigate('Login')
        })
    }
    return (
        <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} start={{x: -10, y: 0.15}} style={styles.container}>
            
            <View style={{flexDirection: 'row', marginTop: 20}}>

            <TouchableOpacity onPress={() => navigation.navigate('Perfil')} style={{
            marginStart: 15}}>
                    <MaterialIcons name="arrow-back-ios" size={27} color="white" />
                </TouchableOpacity>

            <Text style={{marginStart: 80, alignSelf: 'center',
            fontSize: 20, color: 'white', fontWeight: 'bold'}}>Configurações</Text>
            </View>

            <TouchableOpacity onPress={() => navigation.navigate('Mudarusername')} 
            style={{backgroundColor: '#ed450c', width: '95%', justifyContent: 'center',
            alignSelf: 'center', borderRadius: 10, marginTop: 30, height: 50 , elevation: 10}}>

            <Text style={{
                 color: 'white',
                 fontSize: 16,
                 marginStart: 10,
                 fontWeight: 'bold'
            }}
            >Editar nome</Text>

            </TouchableOpacity>

            <TouchableOpacity onPress={() => navigation.navigate('Mudarsenha')} 
            style={{backgroundColor: '#ed450c', width: '95%', justifyContent: 'center',
            alignSelf: 'center', borderRadius: 10, marginTop: 10, height: 50 , elevation: 10}}>

            <Text style={{
                 color: 'white',
                 fontSize: 16,
                 marginStart: 10,
                 fontWeight: 'bold'
            }}
            >Mudar a senha</Text>

            </TouchableOpacity>

            <TouchableOpacity onPress={() => navigation.navigate('Mudardescricao')} 
            style={{backgroundColor: '#ed450c', width: '95%', justifyContent: 'center',
            alignSelf: 'center', borderRadius: 10, marginTop: 10, height: 50 , elevation: 10}}>

            <Text style={{
                 color: 'white',
                 fontSize: 16,
                 marginStart: 10,
                 fontWeight: 'bold'
            }}>Mudar descrição</Text>

            </TouchableOpacity>

            <TouchableOpacity 
            style={{backgroundColor: '#ed450c', width: '95%',
            alignSelf: 'center', borderRadius: 10, marginTop: 10, height: 50, 
            flexDirection: 'row' , elevation: 10}}>

        <MaterialIcons name="widgets" size={27} color="white" style={{justifyContent: 'center',
        alignSelf:'center', marginStart: 10}} />

            <Text style={styles.txt1}>Termos de serviço</Text>
            </TouchableOpacity>

            <TouchableOpacity  
            style={{backgroundColor: '#ed450c', width: '95%',
            alignSelf: 'center', borderRadius: 10, marginTop: 10, height: 50, flexDirection: 'row', elevation: 10}}>

        <MaterialIcons name="shield" size={27} color="white" style={{justifyContent: 'center',
        alignSelf:'center', marginStart: 10}} />

            <Text style={styles.txt1}>Política de privacidade</Text>
            </TouchableOpacity>

            <TouchableOpacity  
            style={{backgroundColor: '#ed450c', width: '95%',alignSelf: 'center',
             borderRadius: 10, marginTop: 10, height: 50, flexDirection: 'row' , elevation: 10}}>

            <MaterialIcons name="groups" size={27} color="white" style={{justifyContent: 'center',
        alignSelf:'center', marginStart: 10}} />

            <Text style={styles.txt1}>Suporte</Text>
            </TouchableOpacity>

            <TouchableOpacity  style={{backgroundColor: 'white', width: '95%',
            alignSelf: 'center', borderRadius: 10, marginTop: 10, height: 50, flexDirection: 'row', elevation: 10}}
            onPress={
                () => logout()
        }>

            <MaterialIcons name="logout" size={27} color="#ed450c" style={{justifyContent: 'center',
        alignSelf:'center', marginStart: 10}} />
            <Text style={{
                  color: '#ed450c',
                  fontSize: 16,
                  marginTop: 13,
                  marginStart: 10,
                  fontWeight: 'bold'
            }}>Sair</Text>
        </TouchableOpacity>
        </LinearGradient>
    )
}

export default Configuracao

const styles = StyleSheet.create({
    container: {
        width: '100%',
        height: '100%',
    },
    txt1: {
        color: 'white',
        fontSize: 16,
        marginTop: 13,
        marginStart: 10,
        fontWeight: 'bold'
    }
})