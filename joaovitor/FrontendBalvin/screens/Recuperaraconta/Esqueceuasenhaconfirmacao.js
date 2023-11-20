import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity } from 'react-native'
import React from 'react'
import { MaterialCommunityIcons } from '@expo/vector-icons';

const Esqueceuasenhaconfirmacao = ({ navigation }) => {
    return (
        <View style={{width: '100%', height: '100%', alignItems: 'center',
        justifyContent: 'center'}}>

            <View style={{width: '100%', justifyContent: 'center', alignItems: 'center',
        marginTop: -100}}>
                <MaterialCommunityIcons name="check-decagram" size={100} color="#ec230d" />
                <Text style={{color: 'gray', fontWeight: 'bold', fontSize: 20, marginTop: 5}} 
                > Conta recuperada com sucesso</Text>
            </View>

            <TouchableOpacity style={{width: '90%', height: 40, backgroundColor: '#ec230d', 
        justifyContent: 'center', alignItems: 'center', borderRadius: 5, marginTop: 10}}
        onPress={() => navigation.navigate('Login')}>
                <Text style={{fontWeight: 'bold', color: 'white'}}>Ir para a tela de login</Text>
            </TouchableOpacity>

        </View>
    )
}


export default Esqueceuasenhaconfirmacao

const styles = StyleSheet.create({})