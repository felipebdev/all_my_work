import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity, ActivityIndicator } from 'react-native'
import React, { useState } from 'react'
import { MaterialIcons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';

const Mudardescricao = ({ navigation }) => {

    const [description, setdescription] = useState('')

    const [loading, setLoading] = useState(false)


    const handleDescription = () => {

        if (description == '') {
            alert('Por favor escreva uma descrição')
        }
        else {
            setLoading(true)
            AsyncStorage.getItem('user').then(
                data => {
                    fetch('http://192.168.0.54:3000/editardescricao', {
                        method: 'post',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            email: JSON.parse(data).user.email,
                            description: description
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.message === "Descrição atualizada com sucesso") {
                                setLoading(false)
                               
                                navigation.navigate('Configuracao')
                            }
                            else if (data.error === "Credenciais inválidas") {
                             
                                setLoading(false)
                                navigation.navigate('Login')
                            }
                            else {
                                setLoading(false)
                                alert("Tente novamente");
                            }
                        })
                        .catch(err => {
                           
                            setLoading(false)
                        })
                }
            )
                .catch(err => {
                 
                    setLoading(false)
                })
        }

    
    }

    return (
        <View style={{width: '100%', height: '100%'}}>
            
        <View style={{width: '100%', height: 50, backgroundColor: '#ec230d', flexDirection: 'row',alignItems: 'center'}}>

        <TouchableOpacity onPress={() => navigation.navigate('Configuracao')} style={{
            marginStart: 15}}>
                    <MaterialIcons name="arrow-back-ios" size={27} color="white" />
                </TouchableOpacity>

            <Text style={{marginStart: 85, color: 'white', fontSize: 18, fontWeight: 'bold'}}>Editar descrição</Text>
        </View>
          
            <Text style={{marginStart: 40, marginTop: 20, fontSize: 20, fontWeight: 'bold'}}>Descricão</Text>

            <TextInput placeholder="Entre com uma descrição" 
                onChangeText={(text) => setdescription(text)}
                multiline={true}
                numberOfLines={5}
                style={{width: 350, height: 40, backgroundColor: 'white', borderRadius: 10, padding: 10, alignSelf: 'center', marginTop: 10}}
            />

{
                loading ? <ActivityIndicator size="large" color="white" style={{marginTop: 25}}/> :
                <TouchableOpacity style={{ width: 340, height: 40, justifyContent: 'center', alignItems: 'center', backgroundColor: '#ec230d',
            borderRadius: 5, alignSelf: 'center', marginTop: 20}} onPress={() => handleDescription()}
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






export default Mudardescricao

const styles = StyleSheet.create({})