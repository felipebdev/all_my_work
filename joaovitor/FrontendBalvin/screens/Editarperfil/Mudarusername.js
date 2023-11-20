import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity, ActivityIndicator} from 'react-native'
import React, { useState } from 'react'
import { MaterialIcons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';

const Mudarusername = ({ navigation }) => {

    const [nome, setNome] = useState('')

    const [loading, setLoading] = useState(false)


    const handleUsername = () => {
        if (nome == '') {
            alert('Por favor, insira o nome de usuário')
        }
        else {
            setLoading(true)
            AsyncStorage.getItem('user')
                .then(data => {
                    fetch('http://192.168.0.54:3000/mudarnome', {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            email: JSON.parse(data).user.email,
                            nome: nome
                        })
                    })
                        .then(res => res.json())
                        .then(
                            data => {
                                if (data.message === "Nome de usuário atualizado com êxito") {
                                    setLoading(false)
                                    navigation.navigate('Configuracao')
                                }
                                else if (data.error === "Credenciais inválidas") {
                                    setLoading(false)
                                    navigation.navigate('Login')
                                }
                                else {
                                    setLoading(false)
                                    alert("O nome de usuário já existe");
                                }
                            }
                        )
                        .catch(err => {
                            
                            setLoading(false)
                        })
                })
                .catch(err => {
                  
                    setLoading(false)
                }
                )
        }

    
    }

    return (
        <View style={{width: '100%', height: '100%'}}>
            
            <View style={{width: '100%', height: 50, backgroundColor: '#ec230d', flexDirection: 'row',alignItems: 'center'}}>

            <TouchableOpacity onPress={() => navigation.navigate('Configuracao')} style={{
            marginStart: 15}}>
                    <MaterialIcons name="arrow-back-ios" size={27} color="white" />
                </TouchableOpacity>

                <Text style={{marginStart: 100, color: 'white', fontSize: 18, fontWeight: 'bold'}}>Editar nome</Text>
            </View>

            <Text style={{marginStart: 40, marginTop: 20, fontSize: 20, fontWeight: 'bold'}}>Nome</Text>
            <TextInput style={{width: 350, height: 40, backgroundColor: 'white', borderRadius: 10, padding: 10, alignSelf: 'center', marginTop: 10}} placeholder="Insira um novo nome"
                onChangeText={(text) => setNome(text)}
            />

            {
                loading ? <ActivityIndicator  size="large" color="white" style={{marginTop: 25}}/> :
                <TouchableOpacity style={{ width: 340, height: 40, justifyContent: 'center', alignItems: 'center', backgroundColor: '#ec230d',
            borderRadius: 5, alignSelf: 'center', marginTop: 20}} onPress={() => handleUsername()}
                >
                    <Text style={{
                        fontSize: 18, 
                        color: 'white',
                        fontWeight: 'bold'
                    }}>
                        Confirmar
                    </Text>
                    </TouchableOpacity>
            }
        </View>
    )
}




export default Mudarusername

const styles = StyleSheet.create({})