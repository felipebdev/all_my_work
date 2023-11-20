import { StyleSheet, Text, View, Image, TextInput, TouchableOpacity, ActivityIndicator} from 'react-native'
import React, { useState } from 'react'
import { MaterialIcons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';

const Mudarsenha = ({ navigation }) => {

    const [oldsenha, setOldsenha] = useState('')
    const [novasenha, setNovasenha] = useState('')
    const [confirmarsenha, setConfirmarsenha] = useState('')
    const [loading, setLoading] = useState(false)


    const handlePasswordChange = () => {
        if (oldsenha === '' || novasenha === '' || confirmarsenha === '') {
            alert('Please fill all the fields')
        } else if (novasenha !== confirmarsenha) {
            alert('New password and confirm new password must be same')
        }
        else {
            setLoading(true)
            AsyncStorage.getItem('user')

                .then(data => {
                    fetch('http://192.168.0.54:3000/mudarasenha', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            "Authorization": 'Bearer ' + JSON.parse(data).tokens
                        },
                        body: JSON.stringify({ email: JSON.parse(data).user.email, oldsenha: oldsenha, novasenha: novasenha })
                    })
                        .then(res => res.json()).then(data => {
                            if (data.message == 'Senha alterada com êxito') {
                                setLoading(false)
                                AsyncStorage.removeItem('user')
                                navigation.navigate('Login')
                            }
                            else {
                                setLoading(false)
                            }
                        }
                        )
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

            <Text style={{marginStart: 100, color: 'white', fontSize: 18, fontWeight: 'bold'}}>Trocar senha</Text>
        </View>
            
            <Text style={{marginTop: 20, fontSize: 20, fontWeight: 'bold', alignSelf: 'center'}} >Escolha uma nova senha</Text>
            
            <TextInput style={{width: 350, height: 40, backgroundColor: 'white', borderRadius: 10, padding: 10, alignSelf: 'center', marginTop: 15}} placeholder="Entre com a senha antiga"  secureTextEntry
                onChangeText={(text) => setOldsenha(text)}
            />
            <TextInput style={{width: 350, height: 40, backgroundColor: 'white', borderRadius: 10, padding: 10, alignSelf: 'center', marginTop: 15}} placeholder="Entre com a nova senha"  secureTextEntry
                onChangeText={(text) => setNovasenha(text)}
            />
            <TextInput  style={{width: 350, height: 40, backgroundColor: 'white', borderRadius: 10, padding: 10, alignSelf: 'center', marginTop: 15}} placeholder="Confirme a nova senha"  secureTextEntry
                onChangeText={(text) => setConfirmarsenha(text)}
            />
            <Text style={{marginStart: 30, marginTop: 10, color:'#ec230d'}}
                onPress={() => navigation.navigate('ForgotPassword_EnterEmail')}
            >Esqueceu a senha?</Text>
         {
                loading ? <ActivityIndicator size="large" color="white" style={{marginTop: 25}}/> :
                <TouchableOpacity style={{ width: 340, height: 40, justifyContent: 'center', alignItems: 'center', backgroundColor: '#ec230d',
            borderRadius: 5, alignSelf: 'center', marginTop: 20}} onPress={() => handlePasswordChange()}
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



export default Mudarsenha

const styles = StyleSheet.create({})
